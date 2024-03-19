import axios from 'axios';
import { rutaApi } from "../Globals.js";
export async function postCall(formData) {
  const respuesta = await axios.post(rutaApi, formData);
  return respuesta;
}
export async function postWithHeadersCall(formData,headers) {
    const respuesta = await axios.post(rutaApi, formData,headers);
    return respuesta;
  }
