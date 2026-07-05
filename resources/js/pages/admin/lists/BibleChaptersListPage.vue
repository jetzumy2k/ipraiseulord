<template>
  <AdminCrudPage :config="config" />
</template>

<script>
import AdminCrudPage from '../../../components/shared/AdminCrudPage.vue';
import { resourceConfigs } from '../config/resourceConfigs';

export default {
  name: 'BibleChaptersListPage',
  components: { AdminCrudPage },
  computed: {
    config() {
      return resourceConfigs['bible-chapters'];
    },
  },
};
</script>
