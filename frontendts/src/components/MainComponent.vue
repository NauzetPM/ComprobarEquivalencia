<template>
  <div class="main-container">
    <el-select v-model="NombreEmpresa" placeholder="Seleccione una opción" class="custom-select">
      <el-option v-for="option in opciones" :value="option.value" :label="option.label" :key="option.label">
      </el-option>
    </el-select>
    <form @submit.prevent="subirFichero">
      <label>
        <input type="file" id="file-input" @change="handleFileChange" class="input-file" />
      </label>
      <br />
      <el-button type="primary" @click="subirFichero" class="custom-button">Subir</el-button>
    </form>

    <div class="loading-container" v-if="loading">
      <v-progress-circular
      color="purple"
      indeterminate
    ></v-progress-circular>
    </div>
  </div>
</template>

<script lang="ts">
import { postCall, postWithHeadersCall } from "../Infraestructure/AxiosCalls";
import { ElSelect, ElOption, ElButton } from "element-plus";
import "element-plus/dist/index.css";
import CryptoJS from "crypto-js";
import { defineComponent } from "vue";
import { AxiosRequestConfig} from "axios";
interface Opcion {
  value: string;
  label: string;
}
export default defineComponent({
  name: "MainComponent",
  components: {
    ElSelect,
    ElOption,
    ElButton,
  },
  data() {
    return {
      selectedFile: null as File | null,
      nombreFile: "",
      opciones: [] as Opcion[],
      json: require("../tablas.json"),
      NombreEmpresa: "",
      fileExtension: "",
      loading: false,
      token: "",
    };
  },
  mounted() {
    this.cargarOpciones();
  },
  methods: {
    async cargarOpciones() {
      const nuevasOpciones = this.json.map((dato: { nombre: string }) => ({
        value: dato.nombre,
        label: dato.nombre,
      }));
      this.opciones = nuevasOpciones;
    },
    async subirFichero() {
      this.loading = true;
      const sePuedeDescargar = await this.subirFicheroCall();
      if (sePuedeDescargar) {
        await this.descargar();
      }
    },
    async descargar() {
      if (!this.selectedFile) {
        this.loading = false;
        return;
      }
      const formData = new FormData();
      formData.append("service", "descargar");
      formData.append("NombreFile", this.nombreFile);
      formData.append("NombreMayorista", this.NombreEmpresa);
      formData.append("token", this.token);
      try {
        const headers: AxiosRequestConfig = {
          responseType: "blob",
        };
        const response = await postWithHeadersCall(formData, headers);
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement("a");
        link.href = url;
        link.setAttribute("download", "descarga.ods");
        document.body.appendChild(link);
        link.click();
      } catch (error) {
        console.error("Error al descargar el archivo:", error);
      } finally {
        this.loading = false;
      }
    },
    async subirFicheroCall() {
      if (!this.selectedFile) {
        this.loading = false;
        alert("No se ha seleccionado el archivo");
        return false;
      }
      this.token = this.generarTokenUnico();
      if (!this.token) {
        this.loading = false;
        alert("No se ha generado el token correctamente");
        return false;
      }
      if (this.NombreEmpresa === "") {
        alert("No se ha seleccionado el Mayorista");
        this.loading = false;
        return false;
      }
      const fileExtensionC = this.selectedFile.name.split(".").pop();
      this.fileExtension = fileExtensionC !== undefined ? fileExtensionC : "";
      this.nombreFile = this.selectedFile.name;

      const formData = new FormData();
      formData.append("service", "comprobarToken");
      formData.append("token", this.token);
      formData.append("NombreMayorista", this.NombreEmpresa);
      const respuesta = await postCall(formData);
      if (respuesta.data == true) {
        return true;
      }
      const CHUNK_SIZE = 1024 * 1024;
      const totalChunks = Math.ceil(this.selectedFile.size / CHUNK_SIZE);
      let chunkIndex = 0;

      const uploadChunk = async (chunk: Blob, chunkIndex: number) => {
        if (this.selectedFile == null) {
          alert("Error con el archivo seleccionado");
          this.loading = false;
          return false;
        }
        const formData = new FormData();
        formData.append(
          "chunk",
          chunk,
          `${this.selectedFile.name}.${chunkIndex}`
        );
        formData.append("totalChunks", totalChunks.toString());
        formData.append("chunkIndex", chunkIndex.toString());
        formData.append("service", "subirFichero");
        formData.append("NombreFile", this.selectedFile.name);
        formData.append("NombreMayorista", this.NombreEmpresa);
        if (chunkIndex == totalChunks - 1) {
          formData.append("token", this.token);
        }
        await postCall(formData);
      };

      const reader = new FileReader();
      reader.onload = async (event) => {
        if (!event.target) {
          return false;
        }
        const fileContent = event.target.result;
        while (chunkIndex < totalChunks) {
          if (this.selectedFile == null) {
            alert("Errir con el archivo seleccionado");
            this.loading = false;
            return false;
          }
          const start = chunkIndex * CHUNK_SIZE;
          const end = Math.min(start + CHUNK_SIZE, this.selectedFile.size);
          if (fileContent != null) {
            const chunk = new Blob([fileContent.slice(start, end)]);
            await uploadChunk(chunk, chunkIndex);
            chunkIndex++;
          }
        }
      };
      reader.readAsArrayBuffer(this.selectedFile);
      return true;
    },
    handleFileChange(event: Event) {
      const target = event.target as HTMLInputElement;
      if (target.files && target.files.length > 0) {
        this.selectedFile = target.files.item(0);
      }
    },

    generarTokenUnico() {
      if (!this.selectedFile) {
        return "";
      }
      const nombreArchivo = this.selectedFile.name;
      const tamañoArchivo = this.selectedFile.size;
      const tipoArchivo = this.selectedFile.type;
      const ultimaModificacion = this.selectedFile.lastModified;

      const concatenacion = `${nombreArchivo}-${tamañoArchivo}-${tipoArchivo}-${ultimaModificacion}`;

      const hash: string = CryptoJS.MD5(concatenacion).toString();

      return hash;
    },
  },
});
</script>

<style src="../styles/MainComponent.css"></style>
