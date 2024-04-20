import './bootstrap';
import 'flowbite';

// import EditorJS from '@editorjs/editorjs'; 
// import Header from '@editorjs/header'; 
// import axios from 'axios';

// let saveBtn = document.getElementById('save-data');
// let contentValueInput = document.getElementById('content_value');

// let editorData = {}; // Variable para almacenar los datos del editor

// if (contentValueInput && contentValueInput.value) {
//   // Si existe un valor en el campo oculto content_value, asignar ese valor a editorData
//   try {
//     editorData = JSON.parse(contentValueInput.value);
//   } catch (error) {
//     console.error('Error parsing JSON from content_value input:', error);
//   }
// }

// const editor = new EditorJS({ 
//   holder: 'editorjs', 
//   data: editorData, // Asignar el valor inicial del editor
//   tools: {
//     header: {
//         class: Header,
//         config: {
//             placeholder: 'Ingresa un header',
//             levels: [1, 2, 3],
//             defaultLevel: 1
//         }
//     }
//   }
// });

// if (saveBtn) {
//   saveBtn.addEventListener('click', (e) => {
//       e.preventDefault();

//       let aTag = e.target;
//       const url = aTag.getAttribute('href');

//       console.log(url);

//       editor.save().then((outputData) => {
//           console.log('Article data: ', outputData)

//           axios({
//               method: 'post',
//               url: url,
//               data: {
//                   _method: 'PUT',
//                   abstract: document.getElementById('abstract').value,
//                   keywords: document.getElementById('keywords').value,
//                   content: outputData
//               }
//           }).then((response) => {
//               console.log('Article updated successfully:', response);
//               // Actualizar el embed con el PDF generado
//               document.getElementById('pdf-embed').src = response.data.pdf_url;
//           }).catch((error) => {
//               console.error('Error updating article:', error);
//           });

//       }).catch((error) => {
//           console.log('Saving failed: ', error)
//       });

//   }, false);
// }



