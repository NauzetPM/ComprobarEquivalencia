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
      <el-progress type="circle" :percentage="progress" :color="colors"></el-progress>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { ipPuerto } from "../Globals.js";
import { ElSelect, ElOption, ElProgress } from "element-plus";
import "element-plus/dist/index.css";

export default {
  components: {
    ElSelect,
    ElOption,
    ElProgress,
  },
  data() {
    return {
      selectedFile: null,
      nombreFile: "",
      opciones: [],
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
    };
  },
  mounted() {
    this.cargarOpciones();
  },
  methods: {
    async cargarOpciones() {
      const nuevasOpciones = this.json.map((dato) => ({
        value: dato.nombre,
        label: dato.nombre,
      }));
      this.opciones = nuevasOpciones;
    },
    async subirFichero() {
      if (this.NombreEmpresa !== "") {
        if (this.selectedFile) {
          try {
            const fileExtensionC = this.selectedFile.name.split(".").pop();
            this.fileExtension = fileExtensionC;

            const CHUNK_SIZE = 1024 * 1024;
            const totalChunks = Math.ceil(this.selectedFile.size / CHUNK_SIZE);
            let chunkIndex = 0;

            const uploadChunk = async (chunk, chunkIndex) => {
              const formData = new FormData();
              formData.append(
                "chunk",
                chunk,
                `${this.selectedFile.name}.${chunkIndex}`
              );
              formData.append("totalChunks", totalChunks.toString());
              formData.append("chunkIndex", chunkIndex.toString());
              formData.append("service", "comprobarFichero");
              formData.append("NombreFile", this.selectedFile.name);
              formData.append("NombreEmpresa", this.NombreEmpresa);
              formData.append("fileExtension", this.fileExtension);

              await axios.post(`http://${ipPuerto}/prueba.php`, formData);
              this.progress = Math.floor(((chunkIndex + 1) * 50) / totalChunks);
            };

            const reader = new FileReader();
            reader.onload = async (event) => {
              if (event.target) {
                const fileContent = event.target.result;
                while (chunkIndex < totalChunks) {
                  const start = chunkIndex * CHUNK_SIZE;
                  const end = Math.min(
                    start + CHUNK_SIZE,
                    this.selectedFile.size
                  );
                  const chunk = new Blob([fileContent.slice(start, end)]);
                  await uploadChunk(chunk, chunkIndex);
                  chunkIndex++;
                }
              }
            };
            reader.readAsArrayBuffer(this.selectedFile);
            this.nombreFile = this.selectedFile.name;
            await this.descargar();
          } catch (error) {
            console.error("Error al subir el archivo:", error);
          }
        }
      } else {
        alert("No se ha seleccionado el Mayorista");
      }
    },
    async descargar() {
      if (this.selectedFile) {
        this.loading = true;
        const formData = new FormData();
        formData.append("service", "descargar");
        formData.append("NombreFile", this.nombreFile);
        formData.append("NombreEmpresa", this.NombreEmpresa);
        formData.append("fileExtension", this.fileExtension);
        try {
          const response = await axios.post(
            `http://${ipPuerto}/prueba.php`,
            formData,
            {
              responseType: "blob",
              onDownloadProgress: (progressEvent) => {
                this.progress =
                  50 +
                  Math.floor((progressEvent.loaded * 50) / progressEvent.total);
              },
            }
          );
          const url = window.URL.createObjectURL(new Blob([response.data]));
          const link = document.createElement("a");
          link.href = url;
          link.setAttribute("download", "descarga.ods");
          document.body.appendChild(link);
          link.click();
        } catch (error) {
          console.error("Error al descargar el archivo:", error);
        } finally {
          //this.loading = false;
        }
      }
    },
    handleFileChange(event) {
      const file = event.target.files?.[0];
      if (file) {
        this.selectedFile = file;
      }
    },
  },
};
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
