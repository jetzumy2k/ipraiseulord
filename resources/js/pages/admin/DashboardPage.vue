<template>
  <div>
    <h1 class="h3 mb-4">Dashboard</h1>

    <div v-if="loading" class="text-center py-5">
      <i class="fas fa-spinner fa-spin fa-2x" />
    </div>

    <template v-else-if="stats">
      <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-3">
          <div class="info-box bg-info">
            <span class="info-box-icon"><i class="fas fa-eye" /></span>
            <div class="info-box-content">
              <span class="info-box-text">Total Visits</span>
              <span class="info-box-number">{{ stats.total_visits }}</span>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="info-box bg-success">
            <span class="info-box-icon"><i class="fas fa-users" /></span>
            <div class="info-box-content">
              <span class="info-box-text">Unique Visitors</span>
              <span class="info-box-number">{{ stats.total_visitors }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-lg-6">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Recent Visits</h3>
            </div>
            <div class="card-body p-0">
              <table class="table table-sm mb-0">
                <thead>
                  <tr>
                    <th>When</th>
                    <th>Page</th>
                    <th>Type</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="visit in stats.recent_visits" :key="visit.id">
                    <td>{{ formatDate(visit.visited_at) }}</td>
                    <td>{{ visit.page_title || visit.page_slug || '—' }}</td>
                    <td>{{ visit.page_type }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Most Visited Pages</h3>
            </div>
            <div class="card-body p-0">
              <table class="table table-sm mb-0">
                <thead>
                  <tr>
                    <th>Page</th>
                    <th>Type</th>
                    <th class="text-end">Visits</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, idx) in stats.most_visited" :key="idx">
                    <td>{{ item.page_title || item.page_slug || '—' }}</td>
                    <td>{{ item.page_type }}</td>
                    <td class="text-end">{{ item.visit_count }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script>
export default {
  name: 'DashboardPage',
  data() {
    return {
      stats: null,
      loading: true,
    };
  },
  mounted() {
    window.axios.get('/dashboard')
      .then(({ data }) => { this.stats = data; })
      .finally(() => { this.loading = false; });
  },
  methods: {
    formatDate(value) {
      return value ? new Date(value).toLocaleString() : '—';
    },
  },
};
</script>
