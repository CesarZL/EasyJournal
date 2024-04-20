import EditorJS from "@editorjs/editorjs";
import Header from '@editorjs/header'; 
import axios from 'axios';

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
            }
        }
    });

    window.editor = editor;
});

