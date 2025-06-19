<template>
    <div class="min-h-screen bg-gray-50">
      <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between items-center h-16">
            <div class="flex items-center">
              <h1 class="text-xl font-semibold text-gray-900">Sistema de Gestão de Alunos</h1>
            </div>
            <div v-if="user" class="flex items-center space-x-4">
              <span class="text-sm text-gray-600">{{ user?.name }} ({{ user?.role }})</span>
              <button
                @click="logout"
                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors"
              >
                Sair
              </button>
            </div>
          </div>
        </div>
      </header>

      <div v-if="!isAuthenticated" class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full space-y-8 p-8">
          <div class="bg-white rounded-lg shadow-md p-8">
            <h2 class="text-2xl font-bold text-center text-gray-900 mb-8">Login</h2>
            <form @submit.prevent="login" class="space-y-6">
              <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input
                  id="email"
                  v-model="loginForm.email"
                  type="email"
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                />
              </div>
              <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Senha</label>
                <input
                  id="password"
                  v-model="loginForm.password"
                  type="password"
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                />
              </div>
              <button
                type="submit"
                :disabled="loading"
                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
              >
                {{ loading ? 'Entrando...' : 'Entrar' }}
              </button>
            </form>
          </div>
        </div>
      </div>

      <main v-if="isAuthenticated" class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow mb-6 p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Filtros</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Nome</label>
              <input
                v-model="filters.nome"
                type="text"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                placeholder="Filtrar por nome"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">CPF</label>
              <input
                v-model="filters.cpf"
                @input="applyCPFMask"
                type="text"
                maxlength="14"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                placeholder="000.000.000-00"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Turma</label>
              <input
                v-model="filters.turma"
                type="text"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                placeholder="Filtrar por turma"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Status</label>
              <select
                v-model="filters.status"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">Todos</option>
                <option value="ativo">Ativo</option>
                <option value="inativo">Inativo</option>
              </select>
            </div>
          </div>
          <div class="mt-4 flex space-x-3">
            <button
              @click="fetchAlunos"
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors"
            >
              Aplicar Filtros
            </button>
            <button
              @click="clearFilters"
              class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors"
            >
              Limpar
            </button>
          </div>
        </div>
        <div class="bg-white rounded-lg shadow mb-6 p-6">
          <div class="flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">Alunos</h3>
            <button
              @click="openModal()"
              class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors"
            >
              Novo Aluno
            </button>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CPF</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Turma</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="aluno in alunos" :key="aluno.id">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ aluno.nome }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ aluno.cpf }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ aluno.turma }}</td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="aluno.status === 'ativo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" 
                          class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                      {{ aluno.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                    <button
                      @click="openModal(aluno)"
                      class="text-blue-600 hover:text-blue-900"
                    >
                      Editar
                    </button>
                    <button
                      v-if="user?.role === 'gestor'"
                      @click="toggleStatus(aluno)"
                      class="text-yellow-600 hover:text-yellow-900"
                    >
                      {{ aluno.status === 'ativo' ? 'Desativar' : 'Ativar' }}
                    </button>
                    <button
                      v-if="user?.role === 'gestor'"
                      @click="deleteAluno(aluno)"
                      class="text-red-600 hover:text-red-900"
                    >
                      Excluir
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div v-if="pagination.total > 0" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
              <button
                @click="changePage(pagination.current_page - 1)"
                :disabled="pagination.current_page <= 1"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
              >
                Anterior
              </button>
              <button
                @click="changePage(pagination.current_page + 1)"
                :disabled="pagination.current_page >= pagination.last_page"
                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
              >
                Próximo
              </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700">
                  Mostrando {{ pagination.from }} a {{ pagination.to }} de {{ pagination.total }} resultados
                </p>
              </div>
              <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                  <button
                    v-for="page in visiblePages"
                    :key="page"
                    @click="changePage(page)"
                    :class="page === pagination.current_page ? 'bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'"
                    class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                  >
                    {{ page }}
                  </button>
                </nav>
              </div>
            </div>
          </div>
        </div>
      </main>

      <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
          <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
              {{ editingAluno ? 'Editar Aluno' : 'Novo Aluno' }}
            </h3>
            <form @submit.prevent="saveAluno" class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Nome</label>
                <input
                  v-model="form.nome"
                  type="text"
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">CPF</label>
                <input
                  v-model="form.cpf"
                  @input="applyCPFMask"
                  type="text"
                  maxlength="14"
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  placeholder="000.000.000-00"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Data de Nascimento</label>
                <input
                  v-model="form.data_nascimento"
                  type="date"
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Turma</label>
                <input
                  v-model="form.turma"
                  type="text"
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                />
              </div>
              <div class="flex justify-end space-x-3 pt-4">
                <button
                  type="button"
                  @click="closeModal"
                  class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  :disabled="loading"
                  class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50"
                >
                  {{ loading ? 'Salvando...' : 'Salvar' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div v-if="toast.show" class="fixed top-4 right-4 z-50">
        <div :class="toast.type === 'success' ? 'bg-green-500' : 'bg-red-500'" 
             class="text-white px-6 py-3 rounded-md shadow-lg">
          {{ toast.message }}
        </div>
      </div>
    </div>
</template>
  
<script setup>
  import { ref, reactive, computed, onMounted, watch } from 'vue'
  import axios from 'axios'

  const isAuthenticated = ref(false)
  const user = ref(null)
  const token = ref(localStorage.getItem('token') || '')
  const loading = ref(false)
  const alunos = ref([])
  const showModal = ref(false)
  const editingAluno = ref(null)
  
  // Formulários
  const loginForm = reactive({
    email: '',
    password: ''
  })
  
  const form = reactive({
    nome: '',
    email: '',
    cpf: '',
    data_nascimento: '',
    turma: ''
  })
  
  const filters = reactive({
    nome: '',
    cpf: '',
    turma: '',
    status: ''
  })

  const formatCPF = (value) => {
    if (!value) return ''

    const cleanValue = value.replace(/\D/g, '')

    if (cleanValue.length <= 11) {
      return cleanValue
        .replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d{1,2})/, '$1-$2')
    }
    
    return cleanValue.slice(0, 11)
      .replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4')
  }
  
  const applyCPFMask = (event) => {
    const input = event.target
    const cursorPosition = input.selectionStart
    const oldValue = input.value
    const newValue = formatCPF(input.value)
    
    input.value = newValue

    if (newValue.length > oldValue.length) {
      input.setSelectionRange(cursorPosition + 1, cursorPosition + 1)
    } else {
      input.setSelectionRange(cursorPosition, cursorPosition)
    }
  }

  const pagination = reactive({
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0,
    from: 0,
    to: 0
  })

  const toast = reactive({
    show: false,
    message: '',
    type: 'success'
  })
  
  const visiblePages = computed(() => {
    const pages = []
    const start = Math.max(1, pagination.current_page - 2)
    const end = Math.min(pagination.last_page, pagination.current_page + 2)
    
    for (let i = start; i <= end; i++) {
      pages.push(i)
    }
    return pages
  })

  watch(() => filters.cpf, (newValue) => {
    filters.cpf = formatCPF(newValue)
  })

  watch(() => form.cpf, (newValue) => {
    form.cpf = formatCPF(newValue)
  })

  const API_BASE = 'http://localhost:8000/api'

  const setAuthHeader = () => {
    if (token.value) {
      axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
    }
  }

  const login = async () => {
    loading.value = true
    try {
      const response = await fetch(`${API_BASE}/auth/login`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(loginForm)
      })
      
      const data = await response.json()
      
      if (data.success) {
        token.value = data.data.access_token
        localStorage.setItem('token', token.value)
        setAuthHeader()
        await getUser()
        isAuthenticated.value = true
        showToast('Login realizado com sucesso!', 'success')
      } else {
        showToast(data.message || 'Erro ao fazer login', 'error')
      }
    } catch (error) {
      showToast('Erro de conexão', 'error')
    } finally {
      loading.value = false
    }
  }
  
  const logout = async () => {
    try {
      await fetch(`${API_BASE}/auth/logout`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token.value}`,
          'Content-Type': 'application/json',
        }
      })
    } catch (error) {
      console.error('Erro ao fazer logout:', error)
    } finally {
      token.value = ''
      localStorage.removeItem('token')
      isAuthenticated.value = false
      user.value = null
      delete axios.defaults.headers.common['Authorization']
    }
  }
  
  const getUser = async () => {
    try {
      const response = await fetch(`${API_BASE}/auth/me`, {
        headers: {
          'Authorization': `Bearer ${token.value}`,
        }
      })
      
      const data = await response.json()
      if (data.success) {
        user.value = data.data
      }
    } catch (error) {
      console.error('Erro ao obter usuário:', error)
    }
  }

  const fetchAlunos = async (page = 1) => {
    loading.value = true
    try {
      const params = new URLSearchParams({
        page: page.toString(),
        per_page: pagination.per_page ? pagination.per_page.toString() : '',
        ...Object.fromEntries(Object.entries(filters).filter(([_, v]) => v !== ''))
      })
      
      const response = await fetch(`${API_BASE}/alunos?${params}`, {
        headers: {
          'Authorization': `Bearer ${token.value}`,
        }
      })
      
      const data = await response.json()
      if (data.success) {
        alunos.value = data.data
        Object.assign(pagination, {
          current_page: data.data.current_page,
          last_page: data.data.last_page,
          per_page: data.data.per_page,
          total: data.data.total,
          from: data.data.from,
          to: data.data.to
        })
      }
    } catch (error) {
      showToast('Erro ao carregar alunos', 'error')
    } finally {
      loading.value = false
    }
  }
  
  const saveAluno = async () => {
    loading.value = true
    try {
      const url = editingAluno.value 
        ? `${API_BASE}/alunos/${editingAluno.value.id}`
        : `${API_BASE}/alunos`
      
      const method = editingAluno.value ? 'PUT' : 'POST'
      
      const response = await fetch(url, {
        method,
        headers: {
          'Authorization': `Bearer ${token.value}`,
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(form)
      })
      
      const data = await response.json()
      if (data.success) {
        showToast(editingAluno.value ? 'Aluno atualizado!' : 'Aluno criado!', 'success')
        closeModal()
        fetchAlunos(pagination.current_page)
      } else {
        showToast(data.message || 'Erro ao salvar aluno', 'error')
      }
    } catch (error) {
      showToast('Erro de conexão', 'error')
    } finally {
      loading.value = false
    }
  }
  
  const deleteAluno = async (aluno) => {
    if (!confirm(`Tem certeza que deseja excluir ${aluno.nome}?`)) return
    
    try {
      const response = await fetch(`${API_BASE}/alunos/${aluno.id}`, {
        method: 'DELETE',
        headers: {
          'Authorization': `Bearer ${token.value}`,
        }
      })
      
      const data = await response.json()
      if (data.success) {
        showToast('Aluno excluído!', 'success')
        fetchAlunos(pagination.current_page)
      } else {
        showToast(data.message || 'Erro ao excluir aluno', 'error')
      }
    } catch (error) {
      showToast('Erro de conexão', 'error')
    }
  }
  
  const toggleStatus = async (aluno) => {
    try {
      const newStatus = aluno.status === 'Aprovado' ? 'Cancelado' : 'Aprovado'
      const response = await fetch(`${API_BASE}/alunos/${aluno.id}/status`, {
        method: 'PATCH',
        headers: {
          'Authorization': `Bearer ${token.value}`,
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ status: newStatus })
      })
      
      const data = await response.json()
      if (data.success) {
        showToast(`Status alterado para ${newStatus}!`, 'success')
        fetchAlunos(pagination.current_page)
      } else {
        showToast(data.message || 'Erro ao alterar status', 'error')
      }
    } catch (error) {
      showToast('Erro de conexão', 'error')
    }
  }

  const openModal = (aluno = null) => {
    editingAluno.value = aluno
    if (aluno) {
      Object.assign(form, {
        nome: aluno.nome,
        cpf: formatCPF(aluno.cpf),
        data_nascimento: aluno.data_nascimento,
        turma: aluno.turma
      })
    } else {
      Object.assign(form, {
        nome: '',
        cpf: '',
        data_nascimento: '',
        turma: ''
      })
    }
    showModal.value = true
  }
  
  const closeModal = () => {
    showModal.value = false
    editingAluno.value = null
  }

  const clearFilters = () => {
    Object.assign(filters, {
      nome: '',
      cpf: '',
      turma: '',
      status: ''
    })
    fetchAlunos(1)
  }
  
  const changePage = (page) => {
    if (page >= 1 && page <= pagination.last_page) {
      fetchAlunos(page)
    }
  }

  const showToast = (message, type = 'success') => {
    toast.message = message
    toast.type = type
    toast.show = true
    setTimeout(() => {
      toast.show = false
    }, 3000)
  }

  watch(filters, () => {
    clearTimeout(window.filterTimeout)
    window.filterTimeout = setTimeout(() => {
      fetchAlunos(1)
    }, 500)
  }, { deep: true })

  onMounted(() => {
    if (token.value) {
      setAuthHeader()
      getUser().then(() => {
        isAuthenticated.value = true
        fetchAlunos()
      })
    }
  })
</script>
  
<style scoped>
  .fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s;
  }
  .fade-enter-from, .fade-leave-to {
    opacity: 0;
  }
</style>