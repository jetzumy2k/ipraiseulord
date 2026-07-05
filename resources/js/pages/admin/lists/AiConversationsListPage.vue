<template>
  <AdminCrudPage :config="config" />
</template>

<script>
import AdminCrudPage from '../../../components/shared/AdminCrudPage.vue';
import { resourceConfigs } from '../config/resourceConfigs';

export default {
  name: 'AiConversationsListPage',
  components: { AdminCrudPage },
  computed: {
    config() {
      return resourceConfigs['ai-conversations'];
    },
  },
};
</script>
