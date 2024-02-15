import EditorJS from '@editorjs/editorjs';

import Header from '@editorjs/header';
import List from '@editorjs/list';
import simpleImageUmd from '@editorjs/simple-image';
import imageUmd from '@editorjs/image';

// npm i @editorjs/header --save
// npm i @editorjs/list --save
// npm i @editorjs/simple-image --save
// npm i @editorjs/image --save


const editor = new EditorJS({
  /**
   * Id of Element that should contain Editor instance
   */
  holder: 'editorjs',
  tools: {
    header: {
      class: Header,
      config: {
        placeholder: 'Header',
        levels: [2, 3, 4],
        defaultLevel: 3,
      },
    },
    imageUmd: {
      class: imageUmd,
      inlineToolbar: true,
    },
    list: {
      class: List,
      inlineToolbar: true,
    },
    simpleImage: {
      class: simpleImageUmd,
      inlineToolbar: true,
    },
  },
});