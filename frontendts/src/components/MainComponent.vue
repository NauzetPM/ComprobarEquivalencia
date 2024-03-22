<template>
  <div class="main-container">
    <el-select
      v-model="nombreMayorista"
      placeholder="Seleccione una opción"
      class="custom-select"
    >
      <el-option
        v-for="option in opciones"
        :value="option.value"
        :label="option.label"
        :key="option.label"
      >
      </el-option>
    </el-select>
    <form @submit.prevent="subirFichero">
      <label>
        <input
          type="file"
          id="file-input"
          @change="handleFileChange"
          class="input-file"
        />
      </label>
      <br />
      <el-button type="primary" @click="subirFichero" class="custom-button">Subir</el-button>
    </form>

    <div class="loading-container" v-if="loading">
      <v-progress-circular color="purple" indeterminate></v-progress-circular>
    </div>
  </div>
</template>

<script lang="ts">
import  LlamadasAxios from "../core/Infraestructure/LlamadasAxios";
import { ElSelect, ElOption, ElButton } from "element-plus";
import "element-plus/dist/index.css";
import CryptoJS from "crypto-js";
import { defineComponent } from "vue";
import { AxiosRequestConfig } from "axios";
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
      llamadas: new LlamadasAxios(),
      selectedFile: null as File | null,
      nombreFile: "",
      opciones: [] as Opcion[],
      json: require("../tablas.json"),
      nombreMayorista: "",
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
    async descargar() {
      if (!this.selectedFile) {
        this.loading = false;
        return;
      }
      const formData = new FormData();
      formData.append("service", "descargar");
      formData.append("NombreFile", this.nombreFile);
      formData.append("NombreMayorista", this.nombreMayorista);
      formData.append("token", this.token);
      try {
        const headers: AxiosRequestConfig = {
          responseType: "blob",
        };
        const response = await this.llamadas.postWithHeadersCall(formData, headers);
        const url = window.URL.createObjectURL(new Blob([response],{ type: 'application/octet-stream' }));
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
    async subirFichero() {
      this.loading = true;
      if (!this.selectedFile) {
        this.loading = false;
        alert("No se ha seleccionado el archivo");
        return;
      }
      this.token = this.generarTokenUnico();
      if (!this.token) {
        this.loading = false;
        alert("No se ha generado el token correctamente");
        return;
      }
      if (this.nombreMayorista === "") {
        alert("No se ha seleccionado el Mayorista");
        this.loading = false;
        return;
      }

      const fileExtensionC = this.selectedFile.name.split(".").pop();
      this.fileExtension = fileExtensionC !== undefined ? fileExtensionC : "";
      this.nombreFile = this.selectedFile.name;

      const formData = new FormData();
      formData.append("service", "comprobarToken");
      formData.append("token", this.token);
      formData.append("NombreMayorista", this.nombreMayorista);
      const respuesta = await this.llamadas.postCall(formData);
      if (respuesta["tokenValido"] == true) {
        await this.descargar();
        return;
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
        formData.append("NombreMayorista", this.nombreMayorista);
        if (chunkIndex == totalChunks - 1) {
          formData.append("token", this.token);
        }
        await this.llamadas.postCall(formData);
      };
      const reader = new FileReader();
      reader.onload = async (event) => {
          if (!event.target) {
            return;
          }
          const fileContent = event.target.result;
          while (chunkIndex < totalChunks) {
            if (this.selectedFile == null) {
              alert("Error con el archivo seleccionado");
              this.loading = false;
              return;
            }
            const start = chunkIndex * CHUNK_SIZE;
            const end = Math.min(start + CHUNK_SIZE, this.selectedFile.size);
            if (fileContent != null) {
              const chunk = new Blob([fileContent.slice(start, end)]);
              await uploadChunk(chunk, chunkIndex);
              chunkIndex++;
            }
          }
          if(chunkIndex==totalChunks){
            await this.descargar();
          }
        };
      reader.readAsArrayBuffer(this.selectedFile);
      return;
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
      const tamaniooArchivo = this.selectedFile.size;
      const tipoArchivo = this.selectedFile.type;
      const ultimaModificacion = this.selectedFile.lastModified;

      const concatenacion = `${nombreArchivo}-${tamaniooArchivo}-${tipoArchivo}-${ultimaModificacion}`;

      const hash: string = CryptoJS.MD5(concatenacion).toString();

      return hash;
    },
  },
});
</script>

<style src="../styles/MainComponent.css"></style>
