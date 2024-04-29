import EditorJS from "@editorjs/editorjs";
import Header from '@editorjs/header'; 
import List from '@editorjs/list';
import CodeTool from '@editorjs/code';
import ImageTool from '@editorjs/image';


window.addEventListener("DOMContentLoaded", (event) => {
    const data = JSON.parse(document.getElementById("content").value || "{}");
    const templateSelect = document.getElementById("template");
    const selectedTemplateInput = document.getElementById("selected-template");

    templateSelect.addEventListener("change", function() {
        selectedTemplateInput.value = this.value;
    });
    
    const editor = new EditorJS({
        holder: "editorjs",

        onChange: async function () {
            const data = await editor.save();

            document.getElementById("content").value = JSON.stringify(data);
        },

        data: data,

        tools: {
            header: {
                class: Header,
                config: {
                    placeholder: 'Ingresa un header',
                    levels: [1, 2, 3],
                    defaultLevel: 1
                }
            },
            list: {
                class: List,
                inlineToolbar: true,
                config: {
                  defaultStyle: 'unordered'
                }
            }, 
            code: CodeTool,
            image: {
                class: ImageTool,
                config: {
                  endpoints: {
                    byFile: '/uploadImage', // Your backend file uploader endpoint
                  }
                }
            },
        }
    });

    window.editor = editor;
});

