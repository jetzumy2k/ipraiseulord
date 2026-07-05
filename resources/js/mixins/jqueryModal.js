import { Modal } from 'bootstrap';

export default {
    methods: {
        showModal(modalId) {
            this.$nextTick(() => {
                const el = document.getElementById(modalId);
                if (el) {
                    Modal.getOrCreateInstance(el).show();
                }
            });
        },

        hideModal(modalId) {
            const el = document.getElementById(modalId);
            if (el) {
                const instance = Modal.getInstance(el);
                if (instance) {
                    instance.hide();
                }
            }
        },
    },
};
