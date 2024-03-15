import axios from 'axios';
import React, { ChangeEvent, FormEvent, useEffect, useState } from 'react';
import { ipPuerto } from './Globals';
import './styles.css';
import Select from 'react-select';
import ReactLoading from 'react-loading';

type Props = {};

const Main = (props: Props) => {
    const [selectedFile, setSelectedFile] = useState<File | null>(null);
    const [nombreFile, setnombreFile] = useState("");
    const [opciones, setOpciones] = useState<any[]>([]);
    const json = require("./tablas.json");
    const [NombreEmpresa, setNombreEmpresa] = useState("");
    const [fileExtension, setfileExtension] = useState("");
    const [clickSubir, setclickSubir] = useState(1);
    const [loading, setLoading] = useState(false); // Estado para controlar la carga

    const handleSelectChange = (selectedOption: any) => {
        setNombreEmpresa(selectedOption.value);
    };

    useEffect(() => {
        const nuevasOpciones: React.SetStateAction<any[]> = [];

        json.forEach((dato: { nombre: any; }) => {
            nuevasOpciones.push({ value: dato.nombre, label: dato.nombre });
        });

        setOpciones(nuevasOpciones);
    }, []);

    async function subirFichero(event: FormEvent<HTMLFormElement>): Promise<void> {
        if (NombreEmpresa !== "") {
            event.preventDefault();
            if (selectedFile) {
                try {
                    const fileExtensionC = selectedFile.name.split('.').pop();
                    setfileExtension("" + fileExtensionC);
                    const CHUNK_SIZE = 1024 * 1024; // Tamaño de cada fragmento (1 MB)
                    const totalChunks = Math.ceil(selectedFile.size / CHUNK_SIZE);
                    let chunkIndex = 0;

                    const uploadChunk = async (chunk: Blob, chunkIndex: number) => {
                        const formData = new FormData();
                        formData.append('chunk', chunk, `${selectedFile.name}.${chunkIndex}`);
                        formData.append('totalChunks', totalChunks.toString());
                        formData.append('chunkIndex', chunkIndex.toString());
                        formData.append('service', 'comprobarFichero');
                        formData.append('NombreFile', selectedFile.name);
                        formData.append('NombreEmpresa', NombreEmpresa);
                        formData.append('fileExtension', "" + fileExtensionC);

                        await axios.post(`http://${ipPuerto}/prueba.php`, formData);
                    };

                    const reader = new FileReader();
                    reader.onload = async (event) => {
                        if (event.target) {
                            const fileContent = event.target.result as ArrayBuffer;
                            while (chunkIndex < totalChunks) {
                                const start = chunkIndex * CHUNK_SIZE;
                                const end = Math.min(start + CHUNK_SIZE, selectedFile.size);
                                const chunk = new Blob([fileContent.slice(start, end)]);
                                await uploadChunk(chunk, chunkIndex);
                                chunkIndex++;
                            }
                        }
                    };
                    reader.readAsArrayBuffer(selectedFile);
                    setnombreFile(selectedFile.name);
                    setclickSubir(clickSubir + 1);
                } catch (error) {
                    console.error('Error al subir el archivo:', error);
                }
            }
        } else {
            alert("No se ha seleccionado el Mayorista");
        }
    }

    useEffect(() => {
        Descargar();
    }, [clickSubir]);

    const Descargar = async () => {
        if (selectedFile) {
            setLoading(true);
            const formData = new FormData();
            formData.append('service', 'descargar');
            formData.append('NombreFile', nombreFile);
            formData.append('NombreEmpresa', NombreEmpresa);
            formData.append('fileExtension', fileExtension);
            try {
                const response = await axios.post(`http://${ipPuerto}/prueba.php`, formData, {
                    responseType: 'blob',
                });
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', "descarga.ods");
                document.body.appendChild(link);
                link.click();
            } catch (error) {
                console.error('Error al descargar el archivo:', error);
            } finally {
                setLoading(false);
            }
        }
    }

    const handleFileChange = (event: ChangeEvent<HTMLInputElement>) => {
        const file = event.target.files?.[0];
        if (file) {
            setSelectedFile(file);
        }
    };

    return (
        <div className="main-container">
            <Select
                className="basic-single"
                classNamePrefix="select"
                defaultValue={opciones[0]}
                isSearchable={true}
                name="tablas"
                options={opciones}
                onChange={handleSelectChange}
            />
            <form onSubmit={subirFichero}>
                <label>
                    <input type="file" id="file-input" onChange={handleFileChange} />
                </label>
                <br />
                <input type="submit" value="Subir" />
            </form>


            <div className="loading-container">
                {loading && (
                    <ReactLoading type={'spinningBubbles'} color={'#007bff'} height={'20%'} width={'20%'} />
                )}
            </div>
        </div>
    );
};

export default Main;

