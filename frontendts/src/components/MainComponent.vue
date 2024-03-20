<template>
  <div class="main-container">
    <el-select
      v-model="NombreEmpresa"
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
      <el-button type="primary" @click="subirFichero" class="custom-button"
        >Subir</el-button
      >
    </form>

    <div class="loading-container" v-if="loading">
      <el-progress
        type="circle"
        :percentage="progress"
        :color="colors"
      ></el-progress>
    </div>
  </div>
</template>

<script lang="ts">
import { postCall, postWithHeadersCall } from "../Infraestructure/AxiosCalls";
import { ElSelect, ElOption, ElProgress, ElButton } from "element-plus";
import "element-plus/dist/index.css";
import CryptoJS from "crypto-js";
import { defineComponent } from "vue";
import { AxiosRequestConfig, AxiosProgressEvent } from "axios";
interface Opcion {
  value: string;
  label: string;
}
export default defineComponent({
  name: "HelloWorld",
  components: {
    ElSelect,
    ElOption,
    ElProgress,
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
      progress: 0,
      colors: [
        { color: "#f56c6c", percentage: 20 },
        { color: "#e6a23c", percentage: 40 },
        { color: "#5cb87a", percentage: 60 },
        { color: "#1989fa", percentage: 80 },
        { color: "#6f7ad3", percentage: 100 },
      ],
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
      this.progress = 0;
      this.loading = true;
      const sePuedeDescargar = await this.subirFicheroCall();
      if (sePuedeDescargar) {
        while (this.progress < 50) {
          await new Promise((resolve) => setTimeout(resolve, 100));
        }
        await this.descargar();
      }
    },
    async descargar() {
      if (this.selectedFile) {
        const formData = new FormData();
        formData.append("service", "descargar");
        formData.append("NombreFile", this.nombreFile);
        formData.append("NombreMayorista", this.NombreEmpresa);
        formData.append("token", this.token);
        try {
          const headers: AxiosRequestConfig = {
            responseType: "blob",
            onDownloadProgress: (progressEvent: AxiosProgressEvent) => {
              // Calcula el progreso
              if (progressEvent.total != undefined) {
                const progress =
                  50 +
                  Math.floor((progressEvent.loaded * 50) / progressEvent.total);
                console.log(`Progreso: ${progress}%`);
              }
            },
          };
          if (this.progress >= 50) {
            const progressInterval = 5000;
            const totalIncrements = 100;
            let increment = 50 / totalIncrements;

            let currentProgress = this.progress;
            const increaseProgress = () => {
              currentProgress += increment;
              this.progress = Math.floor(currentProgress);
              if (currentProgress < 90) {
                setTimeout(increaseProgress, progressInterval);
              }
            };

            setTimeout(increaseProgress, progressInterval);
          }
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
      }
    },
    async subirFicheroCall() {
      if (this.selectedFile instanceof File) {
        const tokenGenerado = this.generarTokenUnico();
        if (tokenGenerado !== undefined) {
          this.token = tokenGenerado;
        }

        if (this.token) {
          const fileExtensionC = this.selectedFile.name.split(".").pop();
          this.fileExtension =
            fileExtensionC !== undefined ? fileExtensionC : "";
          this.nombreFile = this.selectedFile.name;

          const formData = new FormData();
          formData.append("service", "comprobarToken");
          formData.append("token", this.token);
          formData.append("NombreMayorista", this.NombreEmpresa);
          const respuesta = await postCall(formData);
          if (respuesta.data == false) {
            if (this.NombreEmpresa !== "") {
              try {
                const CHUNK_SIZE = 1024 * 1024;
                const totalChunks = Math.ceil(
                  this.selectedFile.size / CHUNK_SIZE
                );

                let chunkIndex = 0;
                const uploadChunk = async (chunk: Blob, chunkIndex: number) => {
                  const formData = new FormData();
                  if (this.selectedFile) {
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
                    this.progress = Math.floor(
                      ((chunkIndex + 1) * 50) / totalChunks
                    );
                  }
                };

                const reader = new FileReader();
                reader.onload = async (event) => {
                  if (event.target) {
                    const fileContent = event.target.result;
                    while (chunkIndex < totalChunks) {
                      const start = chunkIndex * CHUNK_SIZE;
                      if (this.selectedFile instanceof File) {
                        const end = Math.min(
                          start + CHUNK_SIZE,
                          this.selectedFile.size
                        );
                        if (fileContent != null) {
                          const chunk = new Blob([
                            fileContent.slice(start, end),
                          ]);
                          await uploadChunk(chunk, chunkIndex);
                          chunkIndex++;
                        }
                      }
                    }
                  }
                };
                if (this.selectedFile instanceof File) {
                  reader.readAsArrayBuffer(this.selectedFile);
                }
              } catch (error) {
                console.error("Error al subir el archivo:", error);
              }
            } else {
              alert("No se ha seleccionado el Mayorista");
            }
          } else {
            const progressInterval = 1000;
            const totalIncrements = 10;
            let increment = 50 / totalIncrements;

            let currentProgress = 0;
            const increaseProgress = () => {
              currentProgress += increment;
              this.progress = Math.floor(currentProgress);
              if (currentProgress < 50) {
                setTimeout(increaseProgress, progressInterval);
              }
            };

            setTimeout(increaseProgress, progressInterval);
          }
        }
        return true;
      } else {
        alert("No se ha seleccionado el archivo");
        return false;
      }
    },
    handleFileChange(event: Event) {
      const target = event.target as HTMLInputElement;
      if (target.files && target.files.length > 0) {
        const file = target.files[0];
        this.selectedFile = file;
      }
    },

    generarTokenUnico() {
      if (this.selectedFile instanceof File) {
        const nombreArchivo = this.selectedFile.name;
        const tamañoArchivo = this.selectedFile.size;
        const tipoArchivo = this.selectedFile.type;
        const ultimaModificacion = this.selectedFile.lastModified;

        const concatenacion = `${nombreArchivo}-${tamañoArchivo}-${tipoArchivo}-${ultimaModificacion}`;

        const hash: string = CryptoJS.MD5(concatenacion).toString();

        return hash;
      }
    },
  },
});
</script>

<style scoped>
.main-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-start;
  height: 100vh;
  padding: 20px;
}

.input-file {
  border: 1px solid black;
  padding: 10px;
  border-radius: 5px;
}

.basic-single {
  width: 100%;
  max-width: 400px;
  margin-bottom: 20px;
}

.loading-container {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-top: 20px;
  height: 30%;
  width: 30%;
}

.custom-select {
  width: 100%;
  max-width: 400px;
  height: 40px;
}

.custom-button {
  background-color: #2981bb;
  border: none;
  color: white;
  padding: 10px 20px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 4px 2px;
  cursor: pointer;
  border-radius: 5px;
}
</style>
