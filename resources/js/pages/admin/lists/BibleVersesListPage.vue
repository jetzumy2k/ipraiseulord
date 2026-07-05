<template>
  <AdminCrudPage :config="config" />
</template>

<script>
import AdminCrudPage from '../../../components/shared/AdminCrudPage.vue';
import { resourceConfigs } from '../config/resourceConfigs';

export default {
  name: 'BibleVersesListPage',
  components: { AdminCrudPage },
  computed: {
    config() {
      return resourceConfigs['bible-verses'];
    },
  },
};
</script>
