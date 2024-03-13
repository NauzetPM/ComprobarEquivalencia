
import axios from 'axios';
import React, { ChangeEvent, FormEvent, useEffect, useState } from 'react';
import { ipPuerto } from './Globals';
import './styles.css';
import pako from 'pako';
import Select from 'react-select';
type Props = {};

interface DatosHotel {
    Codigo: string;
    Nombre: string;
    Estado: string;
}

const Main = (props: Props) => {
    const [filteredDatos, setFilteredDatos] = useState<DatosHotel[]>([]);
    const [selectedFile, setSelectedFile] = useState<File | null>(null);
    const [totalHoteles, setTotalHoteles] = useState(0);
    const [totalMapeados, setTotalMapeados] = useState(0);
    const [totalMapeadosBlock, setTotalMapeadosBlock] = useState(0);
    const [totalPendientes, setTotalPendientes] = useState(0);
    const [currentPage, setCurrentPage] = useState(1);
    const [itemsPerPage] = useState(100);
    const [nombreBusqueda, setnombreBusqueda] = useState('');
    const [codigoBusqueda, setcodigoBusqueda] = useState('')
    const [totalBusquedasActuales, settotalBusquedasActuales] = useState(0);
    const [paginaBusqueda, setpaginaBusqueda] = useState(0);
    const [nombreFile, setnombreFile] = useState("");
    const [opciones, setOpciones] = useState<any[]>([]);
    const json = require("./tablas.json");
    const [NombreEmpresa, setNombreEmpresa] = useState("");
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
    


    async function fetchData(page: number): Promise<void> {
        if (selectedFile) {

            const formData = new FormData();
            formData.append('service', 'comprobarFichero');
            formData.append('page', currentPage + "");
            formData.append('NombreFile', nombreFile);
            formData.append('NombreEmpresa', NombreEmpresa);
            try {
                const response = await axios.post(`http://${ipPuerto}/prueba.php`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                });
                setFilteredDatos(response.data);
            } catch (error) {
                console.error('Error al subir el archivo:', error);
            }

        }
    }

    async function subirFichero(event: FormEvent<HTMLFormElement>): Promise<void> {
        if(NombreEmpresa!=""){
        event.preventDefault();
        setCurrentPage(1);
        if (selectedFile) {
            try {
                const reader = new FileReader();
                reader.onload = async (event) => {
                    if (event.target) {
                        const fileContent = event.target.result as ArrayBuffer;
                        const compressedFile = pako.gzip(fileContent);
                        const compressedBlob = new Blob([compressedFile], { type: selectedFile.type });
                        const formData = new FormData();
                        const originalFileName = selectedFile.name;
                        const fileExtension = originalFileName.split('.').pop();
                        const uniqueFileName = `${Date.now()}_${Math.floor(Math.random() * 1000)}.${fileExtension}`;
                        formData.append('file', compressedBlob, selectedFile.name);
                        formData.append('service', 'comprobarFichero');
                        formData.append('page', '1');
                        formData.append('NombreFile', uniqueFileName);
                        formData.append('NombreEmpresa', NombreEmpresa);
                        setnombreFile(uniqueFileName);
                        const config = {
                            headers: {
                                'Content-Type': 'multipart/form-data',
                                'Content-Encoding': 'gzip',
                            },
                        };
                        const response = await axios.post(`http://${ipPuerto}/prueba.php`, formData, config);
                        console.log(response.data);
                        setFilteredDatos(response.data);
                    }
                };
                reader.readAsArrayBuffer(selectedFile);
            } catch (error) {
                console.error('Error al subir el archivo:', error);
            }
        }       
    }else{
        alert("No se a seleccionado Mayorista");
    }
    }
    useEffect(() => {
        Descargar();
        Estadisticas();
    }, [nombreFile])
    
    const Descargar = async () => {
        if (selectedFile) {
            const formData = new FormData();
            formData.append('service', 'descargar');
            formData.append('NombreFile', nombreFile);
            formData.append('NombreEmpresa', NombreEmpresa);
            try {
                const response = await axios.post(`http://${ipPuerto}/prueba.php`, formData, {
                    responseType: 'blob', // Indica que la respuesta será un blob
                });
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', "descarga.ods");
                document.body.appendChild(link);
                link.click();
            } catch (error) {
                console.error('Error al descargar el archivo:', error);
            }
        }
    }
    

    const Estadisticas = async () => {
        if (selectedFile) {
            const formData = new FormData();
            formData.append('service', 'obtenerEstadisticas');
            formData.append('NombreFile', nombreFile);
            formData.append('NombreEmpresa', NombreEmpresa);
            try {
                const response = await axios.post(`http://${ipPuerto}/prueba.php`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        'Content-Encoding': 'gzip',
                    },
                });
                setTotalHoteles(response.data.total);
                settotalBusquedasActuales(response.data.total);
                setTotalMapeados(response.data.mapeado);
                setTotalMapeadosBlock(response.data.block);
                setTotalPendientes(response.data.pendiente);
            } catch (error) {
                console.error('Error al subir el archivo:', error);
            }
        }
    };

    const handleFileChange = (event: ChangeEvent<HTMLInputElement>) => {
        const file = event.target.files?.[0];
        if (file) {
            setSelectedFile(file);
        }
    };



    useEffect(() => {
        if (nombreBusqueda) {
            buscarPorNombrePagina();
        } else {
            fetchData(currentPage);
        }

    }, [currentPage]);


    const paginate = (pageNumber: number) => {
        setCurrentPage(pageNumber)
        setpaginaBusqueda(pageNumber);
    };
    async function buscarPorNombrePagina() {
        if (selectedFile) {
            const formData = new FormData();
            formData.append('service', 'comprobarFichero');
            formData.append('nombre', nombreBusqueda);
            formData.append('page', currentPage + "");
            formData.append('NombreFile', nombreFile);
            formData.append('NombreEmpresa', NombreEmpresa);
            try {
                const response = await axios.post(`http://${ipPuerto}/prueba.php`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                });
                setFilteredDatos(response.data.datos);
                settotalBusquedasActuales(response.data.totalBusquedas);
            } catch (error) {
                console.error('Error al subir el archivo:', error);
            }
        }
    }


    async function buscar(tipoBusqueda: string): Promise<void> {
        setCurrentPage(1);
        if(tipoBusqueda==="Nombre"){
            buscarPorNombrePagina();
        }
        if (tipoBusqueda === "Codigo") {
            if (selectedFile) {
                const formData = new FormData();
                formData.append('service', 'comprobarFichero');
                formData.append('codigo', codigoBusqueda);
                formData.append('page', currentPage + "");
                formData.append('NombreFile', nombreFile);
                formData.append('NombreEmpresa', NombreEmpresa);
                try {
                    const response = await axios.post(`http://${ipPuerto}/prueba.php`, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                        },
                    });
                    setFilteredDatos(response.data);
                } catch (error) {
                    console.error('Error al subir el archivo:', error);
                }
            }
        }
    }

    return (
        <div className="main-container">
            <form onSubmit={subirFichero}>
                <label>
                    <input type="file" id="file-input" onChange={handleFileChange} />
                </label>
                <br />
                <input type="submit" value="Subir" />
            </form>
            <Select
                className="basic-single"
                classNamePrefix="select"
                defaultValue={opciones[0]}
                isSearchable={true}
                name="tablas"
                options={opciones}
                onChange={handleSelectChange}
            />
            <div>
                {filteredDatos && (
                    <div>
                        <p>Total Hoteles: {totalHoteles}</p>
                        <p>Total Hoteles Mapeados: {totalMapeados} Porcentaje: {((totalMapeados * 100) / totalHoteles).toFixed(2)}%</p>
                        <p>Total Hoteles Mapeados Block: {totalMapeadosBlock} Porcentaje: {((totalMapeadosBlock * 100) / totalHoteles).toFixed(2)}%</p>
                        <p>Total Hoteles Pendientes: {totalPendientes} Porcentaje: {((totalPendientes * 100) / totalHoteles).toFixed(2)}%</p>
                        {
                            (codigoBusqueda === "") && (
                                <div className="pagination">
                                    <button onClick={() => paginate(currentPage - 1)} disabled={currentPage === 1}>
                                        {'<'}
                                    </button>
                                    <span>Página {currentPage} de {Math.ceil(totalBusquedasActuales / itemsPerPage)}</span>
                                    <button
                                        onClick={() => paginate(currentPage + 1)}
                                        disabled={currentPage * itemsPerPage >= totalBusquedasActuales}
                                    >
                                        {'>'}
                                    </button>
                                    <input
                                        type="number"
                                        placeholder="Pagina"
                                        value={paginaBusqueda}
                                        onChange={(e) => setpaginaBusqueda(+e.target.value)}
                                    />
                                    <button onClick={() => paginate(paginaBusqueda)}>Ir Pagina</button>
                                </div>
                            )
                        }


                        <div>
                            <button
                                onClick={() => {
                                    setnombreBusqueda("");
                                    setcodigoBusqueda("");
                                    fetchData(1);
                                    settotalBusquedasActuales(totalHoteles);
                                }}
                            >Limpiar Busqueda</button>
                        </div>
                        <div>
                            <input
                                type="text"
                                placeholder="Buscar por nombre"
                                value={nombreBusqueda}
                                onChange={(e) => setnombreBusqueda(e.target.value)}
                            />
                            <button onClick={() => buscar("Nombre")}>Buscar</button>
                        </div>
                        <div>
                            <input
                                type="text"
                                placeholder="Buscar por código"
                                value={codigoBusqueda}
                                onChange={(e) => setcodigoBusqueda(e.target.value)}
                            />
                            <button onClick={() => buscar("Codigo")}>Buscar</button>
                        </div>
                        <table className="default">
                            <tbody>
                                <tr>
                                    <td>Codigo</td>
                                    <td>Nombre</td>
                                    <td>Estado</td>
                                </tr>
                                {filteredDatos.map((dato) => (
                                    <tr key={dato.Codigo}>
                                        <td>{dato.Codigo}</td>
                                        <td>{dato.Nombre}</td>
                                        <td className={dato.Estado}>{dato.Estado}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                )}
            </div>
        </div>
    );
};

export default Main;



