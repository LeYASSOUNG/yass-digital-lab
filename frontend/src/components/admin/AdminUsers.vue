<template>
  <div class="fade-in">
    <div class="glass" style="padding: 28px; border-radius: 20px;">
      <div class="flex justify-between items-center mb-6" style="flex-wrap: wrap; gap: 16px;">
        <div>
          <h3 style="font-size: 1.3rem; margin: 0; font-weight: 800;">👑 Gestion des Utilisateurs & Rôles</h3>
          <p style="color: var(--color-text-light); font-size: 0.85rem; margin: 2px 0 0;">Attribuez des rôles (Client, Admin, Créateur, Éditeur, Support, Super Admin) en temps réel.</p>
        </div>
        <div class="flex items-center gap-3" style="flex-wrap: wrap;">
          <div style="position: relative; width: 220px;">
            <input v-model="searchUserQuery" type="text" placeholder="Rechercher nom, email..." style="width: 100%; padding: 6px 12px 6px 15px; border-radius: 8px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.82rem;" />
          </div>
          <button @click="exportUsersCSV" class="btn btn-secondary flex items-center gap-1" style="padding: 6px 12px; font-size: 0.8rem;">
            Exporter CSV
          </button>
          <span style="background: rgba(212,175,55,0.15); color: var(--color-accent); padding: 6px 14px; border-radius: 999px; font-weight: 800; font-size: 0.82rem; white-space: nowrap;">
            {{ filteredUsers.length }} utilisateur(s)
          </span>
        </div>
      </div>

      <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 700px;">
          <thead>
            <tr style="border-bottom: 2px solid var(--color-border);">
              <th style="padding: 12px; text-align: left;">ID</th>
              <th style="padding: 12px; text-align: left;">Utilisateur</th>
              <th style="padding: 12px; text-align: left;">Email</th>
              <th style="padding: 12px; text-align: left;">Rôle Actuel</th>
              <th style="padding: 12px; text-align: left;">Attribuer un Rôle</th>
              <th style="padding: 12px; text-align: left;">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="u in filteredUsers" :key="u.id" style="border-bottom: 1px solid var(--color-border);">
              <td style="padding: 12px; color: var(--color-text-light);">#{{ u.id }}</td>
              <td style="padding: 12px; font-weight: 600;">
                <div class="flex items-center gap-3">
                  <div style="width: 34px; height: 34px; border-radius: 50%; overflow: hidden; border: 1px solid var(--color-accent); flex-shrink: 0; background: var(--color-bg);">
                    <img v-if="u.avatar" :src="u.avatar" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;" />
                    <div v-else style="width: 100%; height: 100%; background: var(--color-accent); color: #050811; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem;">
                      {{ u.name ? u.name.charAt(0).toUpperCase() : 'U' }}
                    </div>
                  </div>
                  <span>{{ u.name }}</span>
                </div>
              </td>
              <td style="padding: 12px;">{{ u.email }}</td>
              <td style="padding: 12px;">
                <span v-if="u.role === 'super_admin'" style="background: linear-gradient(135deg, #F0CC55, #D4AF37); color: #050811; padding: 3px 10px; border-radius: 999px; font-weight: 800; font-size: 0.75rem;">
                  👑 Super Admin
                </span>
                <span v-else-if="u.role === 'admin'" style="background: rgba(59,130,246,0.15); color: #3b82f6; padding: 3px 10px; border-radius: 999px; font-weight: 700; font-size: 0.75rem;">
                  🛡️ Admin
                </span>
                <span v-else-if="u.role === 'creator'" style="background: rgba(16,185,129,0.15); color: #10b981; padding: 3px 10px; border-radius: 999px; font-weight: 700; font-size: 0.75rem;">
                  🛠️ Créateur
                </span>
                <span v-else-if="u.role === 'editor'" style="background: rgba(168,85,247,0.15); color: #a855f7; padding: 3px 10px; border-radius: 999px; font-weight: 700; font-size: 0.75rem;">
                  📝 Rédacteur
                </span>
                <span v-else-if="u.role === 'support'" style="background: rgba(236,72,153,0.15); color: #ec4899; padding: 3px 10px; border-radius: 999px; font-weight: 700; font-size: 0.75rem;">
                  🎧 Support
                </span>
                <span v-else style="background: rgba(255,255,255,0.1); color: var(--color-text-light); padding: 3px 10px; border-radius: 999px; font-size: 0.75rem;">
                  👤 Client
                </span>
              </td>
              <td style="padding: 12px;">
                <select :value="u.role" @change="changeRole(u.id, $event.target.value)" style="padding: 5px 10px; border-radius: 8px; border: 1px solid var(--color-border); background: var(--color-bg); color: var(--color-text); font-size: 0.8rem; cursor: pointer;">
                  <option value="client">Client</option>
                  <option value="creator">Créateur</option>
                  <option value="editor">Rédacteur</option>
                  <option value="support">Support</option>
                  <option value="admin">Admin</option>
                  <option value="super_admin">Super Admin</option>
                </select>
              </td>
              <td style="padding: 12px;">
                <button v-if="u.id !== currentUserId" @click="deleteUser(u.id)" style="background: rgba(239,68,68,0.1); color: #ef4444; border: 1px solid #ef4444; padding: 4px 10px; border-radius: 6px; cursor: pointer; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 4px;">
                  🗑️ Supprimer
                </button>
                <span v-else style="font-size: 0.78rem; color: var(--color-text-light); font-style: italic;">(Vous)</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import api from '../../api';
import { useToastStore } from '../../stores/toast';

const props = defineProps({
  currentUserId: {
    type: Number,
    required: true
  }
});

const toastStore = useToastStore();
const usersList = ref([]);
const searchUserQuery = ref('');
const emit = defineEmits(['count-updated']);

const filteredUsers = computed(() => {
  if (!searchUserQuery.value.trim()) return usersList.value;
  const q = searchUserQuery.value.toLowerCase();
  return usersList.value.filter(u => u.name?.toLowerCase().includes(q) || u.email?.toLowerCase().includes(q) || u.role?.toLowerCase().includes(q));
});

const loadUsers = async () => {
  try {
    const res = await api.get('/users');
    usersList.value = Array.isArray(res.data) ? res.data : (res.data.data || []);
    emit('count-updated', usersList.value.length);
  } catch(e) { 
    usersList.value = []; 
  }
};

const changeRole = async (userId, newRole) => {
  try {
    const res = await api.put(`/users/${userId}/role`, { role: newRole });
    toastStore.showToast(res.data.message || 'Rôle mis à jour !', 'success');
    await loadUsers();
  } catch(e) { 
    toastStore.showToast('Erreur lors de la modification du rôle.', 'error'); 
  }
};

const deleteUser = async (userId) => {
  if (confirm('Supprimer définitivement cet utilisateur ?')) {
    try {
      const res = await api.delete(`/users/${userId}`);
      toastStore.showToast(res.data.message || 'Utilisateur supprimé.', 'info');
      await loadUsers();
    } catch(e) { 
      toastStore.showToast('Erreur lors de la suppression.', 'error'); 
    }
  }
};

const exportUsersCSV = () => {
  if (usersList.value.length === 0) {
    toastStore.showToast('Aucun utilisateur à exporter.', 'info');
    return;
  }
  const headers = ['ID', 'Nom', 'Email', 'Role', 'Entreprise', 'Date'];
  const rows = usersList.value.map(u => [u.id, `"${u.name.replace(/"/g, '""')}"`, u.email, u.role, `"${(u.company || '').replace(/"/g, '""')}"`, u.created_at || '']);
  const csvContent = 'data:text/csv;charset=utf-8,' + [headers.join(','), ...rows.map(r => r.join(','))].join('\n');
  const encodedUri = encodeURI(csvContent);
  const link = document.createElement('a');
  link.setAttribute('href', encodedUri);
  link.setAttribute('download', `utilisateurs_systeme_${new Date().toISOString().slice(0,10)}.csv`);
  document.body.appendChild(link);
  link.click();
  link.remove();
  toastStore.showToast('Export CSV des utilisateurs téléchargé !', 'success');
};

onMounted(() => {
  loadUsers();
});
</script>
