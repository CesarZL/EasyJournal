import EditorJS from '@editorjs/editorjs'; 
import Header from '@editorjs/header'; 
import List from '@editorjs/list'; 
import axios from 'axios';

let saveBtn = document.getElementById('save-data');

const editor = new EditorJS({ 
  /** 
   * Id of Element that should contain the Editor 
   */ 
  holder: 'editorjs', 

  /** 
   * Available Tools list. 
   * Pass Tool's class or Settings object for each Tool you want to use 
   */ 
  tools: {
    header: {
        class: Header,
        config: {
            placeholder: 'Ingresa un header',
            levels: [1, 2, 3],
            defaultLevel: 1
        }
    }
  },

})

if (saveBtn) {
  saveBtn.addEventListener('click', (e) => {
    e.preventDefault();

    let aTag = e.target;
    const url = aTag.getAttribute('href');

    console.log(url);

    editor.save().then((outputData) => {
      console.log('Article data: ', outputData)

      // Enviar una petición POST pero incluir _method: 'PUT' en los datos
      axios({
        method: 'post',
        url: url,
        data: {
          _method: 'PUT',
          content: outputData
        }
      }).then((response) => {
        console.log('Article updated successfully:', response);
        // Actualizar el embed con el PDF generado
        document.getElementById('pdf-embed').src = response.data.pdf_url;
      }).catch((error) => {
        console.error('Error updating article:', error);
      });

    }).catch((error) => {
      console.log('Saving failed: ', error)
    });

  }, false);
}


