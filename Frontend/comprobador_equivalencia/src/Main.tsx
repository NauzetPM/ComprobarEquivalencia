import axios from 'axios';
import React, { ChangeEvent, FormEvent, useState } from 'react'
import { ipPuerto } from './Globals';
import './styles.css';
type Props = {}

interface DatosHotel {
    Codigo: string,
    Nombre: string,
    Estado: string
}
const Main = (props: Props) => {
    const [cargarDatos, setcargarDatos] = useState<DatosHotel[]>([]);
    const [selectedFile, setSelectedFile] = useState<File>();

    async function subirFichero(event: FormEvent<HTMLFormElement>): Promise<void> {
        event.preventDefault();

        if (selectedFile) {
            const formData = new FormData();
            formData.append('file', selectedFile);
            formData.append('service',"comprobarFichero");
            try {
                const response = await axios.post(
                    `http://${ipPuerto}/prueba.php`,
                        formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                        },
                    }
                );
                setcargarDatos(response.data);
            } catch (error) {
                console.error('Error al subir el archivo:', error);
            }
        }
    }
    const handleFileChange = (event: ChangeEvent<HTMLInputElement>) => {
        const file = event.target.files?.[0];
        setSelectedFile(file);
    };

    return (
        <div className="main-container">
            <form onSubmit={subirFichero}>
                <label>
                    <input type="file" id="file-input" onChange={handleFileChange} />
                </label>
                <br />
                <input type="submit" value="Subir" />
            </form>
            <div >
                {cargarDatos && (
                    <table className="default">
                        <tbody>
                            <tr>
                                <td>Codigo</td>
                                <td>Nombre</td>
                                <td>Estado</td>
                            </tr>

                            {cargarDatos.map((dato) => (
                                <tr key={dato.Codigo}>
                                    <td>{dato.Codigo}</td>
                                    <td>{dato.Nombre}</td>
                                    <td>{dato.Estado}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                )}
            </div>
        </div>
    );
};

export default Main

