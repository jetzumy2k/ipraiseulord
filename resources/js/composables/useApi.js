import { ref } from 'vue';
import axios from '../bootstrap';

export function useApi() {
    const loading = ref(false);
    const error = ref(null);

    const handleError = (err) => {
        if (err.response?.status === 422 && err.response.data?.errors) {
            const messages = Object.values(err.response.data.errors).flat();
            error.value = messages.join(' ');
        } else if (err.response?.data?.message) {
            error.value = err.response.data.message;
        } else {
            error.value = err.message || 'An unexpected error occurred.';
        }
        throw err;
    };

    const get = async (url, config = {}) => {
        loading.value = true;
        error.value = null;
        try {
            const response = await axios.get(url, config);
            return response.data;
        } catch (err) {
            handleError(err);
        } finally {
            loading.value = false;
        }
    };

    const post = async (url, data = {}, config = {}) => {
        loading.value = true;
        error.value = null;
        try {
            const response = await axios.post(url, data, config);
            return response.data;
        } catch (err) {
            handleError(err);
        } finally {
            loading.value = false;
        }
    };

    const put = async (url, data = {}, config = {}) => {
        loading.value = true;
        error.value = null;
        try {
            const response = await axios.put(url, data, config);
            return response.data;
        } catch (err) {
            handleError(err);
        } finally {
            loading.value = false;
        }
    };

    const del = async (url, config = {}) => {
        loading.value = true;
        error.value = null;
        try {
            const response = await axios.delete(url, config);
            return response.data;
        } catch (err) {
            handleError(err);
        } finally {
            loading.value = false;
        }
    };

    const upload = async (url, formData, config = {}) => {
        loading.value = true;
        error.value = null;
        try {
            const response = await axios.post(url, formData, {
                ...config,
                headers: {
                    'Content-Type': 'multipart/form-data',
                    ...config.headers,
                },
            });
            return response.data;
        } catch (err) {
            handleError(err);
        } finally {
            loading.value = false;
        }
    };

    return {
        loading,
        error,
        get,
        post,
        put,
        del,
        upload,
    };
}
