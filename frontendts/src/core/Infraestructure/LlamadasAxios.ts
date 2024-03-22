import axios from 'axios';
import { AxiosRequestConfig } from 'axios';
import { rutaApi } from "../../Globals";
import { RespuestaApi } from './TypeRespuestaApi';

class LlamadasAxios {

  async postCall(formData: FormData) {
    const respuesta = await axios.post(rutaApi, formData);
    return respuesta.data;
  }

  async postWithHeadersCall(formData: FormData, headers: AxiosRequestConfig) {
    const respuesta = await axios.post(rutaApi, formData, headers);
    const respuestaApi:RespuestaApi=respuesta.data;
    return respuesta.data;
  }
}

export default LlamadasAxios;

/*
export async function postCall(formData:FormData) {
  const respuesta = await axios.post(rutaApi, formData);
  return respuesta.data;
}
export async function postWithHeadersCall(formData:FormData,headers:AxiosRequestConfig) {
    const respuesta = await axios.post(rutaApi, formData,headers);
    return respuesta.data;
}*/
