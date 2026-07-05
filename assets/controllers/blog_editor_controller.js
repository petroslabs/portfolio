import { Controller } from '@hotwired/stimulus';
import Editor from '@toast-ui/editor';
import '@toast-ui/editor/dist/toastui-editor.css';
import '@toast-ui/editor/dist/theme/toastui-editor-dark.css';

// Éditeur WYSIWYG pour le contenu Markdown des articles de blog (admin).
// Le textarea réel du formulaire reste la source de vérité soumise au
// serveur : l'éditeur ne fait que le synchroniser en Markdown à chaque
// changement, pour ne rien changer côté back (contentFr/contentEn stockés
// en Markdown brut, convertis en HTML à l'affichage via league/commonmark).
export default class extends Controller {
    static targets = ['textarea', 'container'];

    connect() {
        this.editor = new Editor({
            el: this.containerTarget,
            height: '400px',
            initialEditType: 'wysiwyg',
            previewStyle: 'vertical',
            initialValue: this.textareaTarget.value,
            events: {
                change: () => {
                    this.textareaTarget.value = this.editor.getMarkdown();
                },
            },
        });

        this.submitListener = () => {
            this.textareaTarget.value = this.editor.getMarkdown();
        };
        this.textareaTarget.form?.addEventListener('submit', this.submitListener);

        this.textareaTarget.style.display = 'none';
    }

    disconnect() {
        this.textareaTarget.form?.removeEventListener('submit', this.submitListener);
        this.editor?.destroy();
    }
}
