import axios from 'axios';
import { AxiosRequestConfig } from 'axios';
import { rutaApi } from "../Globals";
export async function postCall(formData:FormData) {
  const respuesta = await axios.post(rutaApi, formData);
  return respuesta;
}
export async function postWithHeadersCall(formData:FormData,headers:AxiosRequestConfig) {
    const respuesta = await axios.post(rutaApi, formData,headers);
    return respuesta;
  }
