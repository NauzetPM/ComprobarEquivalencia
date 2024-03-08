import React, { FormEvent, useState } from 'react'

type Props = {}

const Main = (props: Props) => {
    const [cargarDatos, setcargarDatos] = useState(null);
    function subirFichero(event: FormEvent<HTMLFormElement>): void {
        alert("SubirFichero");
    }
    const datos = [
        {Codigo: 1, Nombre: 'Hotel 1', Estado: 'Pendiente'},
        {Codigo: 2, Nombre: 'Hotel 2', Estado: 'Mapeado'},
        {Codigo: 3, Nombre: 'Hotel 3', Estado: 'Mapeado Block'},
      ];
    return (
        <div>
            <form onSubmit={subirFichero}>
                <label>
                    Subi Fichero csv:
                    <input type="file" />
                </label>
                <br />
                <input type="submit" value="Subir" />
            </form>
            <div>
                <table className="default">
                    <tr>
                        <td>Codigo</td>
                        <td>Nombre</td>
                        <td>Estado</td>
                    </tr>
                    {datos.map((dato) =>       
                    <tr>
                        <td>{dato.Codigo}</td>
                        <td>{dato.Nombre}</td>
                        <td>{dato.Estado}</td>
                    </tr>
                    )}
                </table>
            </div>
        </div>
    )
}

export default Main

