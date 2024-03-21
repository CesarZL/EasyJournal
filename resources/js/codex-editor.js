import EditorJS from '@editorjs/editorjs'; 
import Header from '@editorjs/header'; 
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
  // data: {"time":1711016511431,"blocks":[{"id":"enhzBDQUUN","type":"header","data":{"text":"Seccion 1","level":1}},{"id":"UAeesoNxf2","type":"paragraph","data":{"text":"texto de secci\u00f3n 1"}},{"id":"Z4HaUFevl2","type":"header","data":{"text":"Seccion2","level":1}},{"id":"hDFDDPfvrl","type":"paragraph","data":{"text":"texto de seccion 2"}},{"id":"hqIXe8vhUf","type":"header","data":{"text":"Subseccion1_de_seccion_2","level":2}},{"id":"jR3bfkG_gS","type":"paragraph","data":{"text":"texto de subseccion 1 de seccion 2,&nbsp; &nbsp; &nbsp;"}},{"id":"xwklv-enZE","type":"header","data":{"text":"&nbsp;Subseccion2_de_seccion_2","level":1}},{"id":"er3OKmhk9r","type":"paragraph","data":{"text":"texto de&nbsp;Subseccion2_de_seccion_2"}},{"id":"Fm-uRL_V0Y","type":"header","data":{"text":"Subsubseccion1_deseccion2","level":3}},{"id":"--WhKeNU3y","type":"paragraph","data":{"text":"texto de la subsubseccion"}},{"id":"CDmwBN6vmh","type":"header","data":{"text":"Seccion3","level":1}},{"id":"BTtmvYXtuY","type":"paragraph","data":{"text":"texto de seccion 3"}}],"version":"2.29.0"}  

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


