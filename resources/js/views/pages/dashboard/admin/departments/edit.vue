<template>
    <main class="flex-1 relative overflow-y-auto py-6 focus:outline-none" tabindex="0">
        <form @submit.prevent="saveCompanies">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-5">
                <div class="md:flex md:items-center md:justify-between">
                    <div class="flex-1 min-w-0">
                        <h1 class="py-0.5 text-2xl font-semibold text-gray-900">{{ $t('Edit Companies') }}</h1>
                    </div>
                    <div class="mt-4 flex md:mt-0 md:ml-4">
                        <button
                            class="btn btn-red shadow-sm rounded-md"
                            type="button"
                            @click="deleteCompaniesModal = true"
                        >
                            {{ $t('Delete Companies') }}
                        </button>
                    </div>
                </div>
            </div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mt-6 shadow sm:rounded-lg">
                    <loading :status="loading"/>
                    <div class="bg-white md:grid md:grid-cols-3 md:gap-6 px-4 py-5 sm:p-6">
                        <div class="md:col-span-1">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">{{ $t('Companies details') }}</h3>
                            <p class="mt-1 text-sm leading-5 text-gray-500">
                                {{ $t('Companies details and settings') }}.
                            </p>
                        </div>
                        <div class="mt-5 md:mt-0 md:col-span-2">
                            <div class="grid grid-cols-3 gap-6">
                                <div class="col-span-3">
                                    <label class="block text-sm font-medium leading-5 text-gray-700" for="name">{{ $t('Name') }}</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <input
                                            id="name"
                                            v-model="Companies.name"
                                            :placeholder="$t('Name')"
                                            class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                                            required
                                        >
                                    </div>
                                </div>
                                <div class="col-span-3">
                                    <label class="block text-sm font-medium leading-5 text-gray-700" for="all_agents">{{ $t('All agents') }}</label>
                                    <input-switch
                                        id="all_agents"
                                        v-model="Companies.all_agents"
                                        :disabled-label="$t('Only selected agents')"
                                        :enabled-label="$t('All agents')"
                                    ></input-switch>
                                    <div class="mt-2 relative text-xs text-gray-500">
                                        {{ $t('Allows access to the Companies to all agents, or exclusively to a specific group of agents') }}.
                                    </div>
                                </div>
                                <div class="col-span-3">
                                    <label class="block text-sm font-medium leading-5 text-gray-700" for="public">{{ $t('Visibility') }}</label>
                                    <input-switch
                                        id="public"
                                        v-model="Companies.public"
                                        :disabled-label="$t('The Companies is private')"
                                        :enabled-label="$t('The Companies is public')"
                                    ></input-switch>
                                    <div class="mt-2 relative text-xs text-gray-500">
                                        {{ $t('If the Companies is public, it allows users to select this Companies when creating the Tickets, otherwise only agents can reassign to this Companies') }}.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <template v-if="!Companies.all_agents">
                            <div class="md:col-span-3">
                                <div class="py-3">
                                    <div class="border-t border-gray-200"></div>
                                </div>
                            </div>
                            <div class="md:col-span-1">
                                <h3 class="text-lg font-medium leading-6 text-gray-900">{{ $t('Companies agents') }}</h3>
                                <p class="mt-1 text-sm leading-5 text-gray-500">
                                    {{ $t('List of agents assigned to the Companies') }}.
                                </p>
                            </div>
                            <div class="mt-5 md:mt-0 md:col-span-2">
                                <div class="grid grid-cols-3 gap-6">
                                    <div class="col-span-3">
                                        <div :style="{maxHeight: '200px'}" class="flex flex-col overflow-y-auto">
                                            <template v-for="user in users">
                                                <div class="flex items-center px-6 py-3 hover:bg-gray-100 cursor-pointer rounded" @click="selectAgent(user.id)">
                                                    <div>
                                                        <div class="flex items-center justify-center">
                                                            <svg-vue v-if="Companies.agents.includes(user.id)" class="w-5 h-5 text-green-400" icon="font-awesome.check-circle-solid"></svg-vue>
                                                            <div v-else class="w-5 h-5 p-1 overflow-hidden rounded-full border"></div>
                                                        </div>
                                                    </div>
                                                    <div class="flex flex-col ml-6">
                                                        <p class="text-sm leading-5 text-gray-700 group-hover:text-gray-900">{{ user.name }}</p>
                                                        <p class="text-xs leading-4 text-gray-500 group-hover:text-gray-700 group-focus:underline transition ease-in-out duration-150">{{ user.email }}</p>
                                                    </div>
                                                    <div class="ml-auto">
                                                        <img
                                                            :src="user.avatar === 'gravatar' ? user.gravatar : user.avatar"
                                                            alt="User avatar"
                                                            class="inline-block rounded-full h-8 w-8"
                                                        />
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div class="bg-gray-100 text-right px-4 py-3 sm:px-6">
                        <span class="inline-flex">
                            <router-link
                                class="btn btn-secondary shadow-sm rounded-md mr-4"
                                to="/dashboard/admin/Companies"
                            >
                                {{ $t('Close') }}
                            </router-link>
                            <button
                                class="btn btn-green shadow-sm rounded-md"
                                type="submit"
                            >
                                {{ $t('Edit Companies') }}
                            </button>
                        </span>
                    </div>
                </div>
            </div>
        </form>
        <div v-show="deleteCompaniesModal" class="fixed z-20 inset-0 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <transition
                    duration="300"
                    enter-active-class="ease-out duration-300"
                    enter-class="opacity-0"
                    enter-to-class="opacity-100"
                    leave-active-class="ease-in duration-200"
                    leave-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <div v-show="deleteCompaniesModal" class="fixed inset-0 transition-opacity">
                        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                    </div>
                </transition>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>&#8203;
                <transition
                    enter-active-class="ease-out duration-300"
                    enter-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-active-class="ease-in duration-200"
                    leave-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                >
                    <div
                        v-show="deleteCompaniesModal"
                        aria-labelledby="modal-headline"
                        aria-modal="true"
                        class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                        role="dialog"
                    >
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg-vue class="h-6 w-6 pb-1 text-red-600" icon="font-awesome.exclamation-triangle-light"></svg-vue>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 id="modal-headline" class="text-lg leading-6 font-medium text-gray-900">
                                        {{ $t('Delete Companies') }}
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-sm leading-5 text-gray-500">
                                            {{ $t('Are you sure you want to delete the Companies?') }}
                                            {{ $t('All data will be permanently removed') }}.
                                            {{ $t('Tickets with this Companies will be changed to "Without Companies"') }}.
                                            {{ $t('This action cannot be undone') }}.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-100 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button
                                class="btn btn-red mr-2 sm:mr-0"
                                type="button"
                                @click="deleteCompanies"
                            >
                                {{ $t('Delete Companies') }}
                            </button>
                            <button
                                class="btn btn-white mr-0 sm:mr-2"
                                type="button"
                                @click="deleteCompaniesModal = false"
                            >
                                {{ $t('Cancel') }}
                            </button>
                        </div>
                    </div>
                </transition>
            </div>
        </div>
    </main>
</template>

<script>
export default {
    name: "edit",
    metaInfo() {
        return {
            title: this.$i18n.t('Edit Companies')
        }
    },
    data() {
        return {
            loading: true,
            deleteCompaniesModal: false,
            users: [],
            Companies: {
                name: null,
                all_agents: false,
                public: true,
                agents: [],
            },
        }
    },
    mounted() {
        this.getUsers();
    },
    methods: {
        saveCompanies() {
            const self = this;
            self.loading = true;
            axios.put('api/dashboard/admin/Companies/' + self.$route.params.id, self.Companies).then(function (response) {
                self.loading = false;
                self.$notify({
                    title: self.$i18n.t('Success').toString(),
                    text: self.$i18n.t('Data updated correctly').toString(),
                    type: 'success'
                });
                self.Companies = response.data.Companies;
            }).catch(function () {
                self.loading = false;
            });
        },
        getUsers() {
            const self = this;
            axios.get('api/dashboard/admin/Companies/users').then(function (response) {
                self.users = response.data;
                self.getCompanies();
            }).catch(function () {
                self.loading = false;
            });
        },
        getCompanies() {
            const self = this;
            self.loading = true;
            axios.get('api/dashboard/admin/Companies/' + self.$route.params.id).then(function (response) {
                self.Companies = response.data;
                self.loading = false;
            }).catch(function () {
                self.loading = false;
            });
        },
        deleteCompanies() {
            const self = this;
            axios.delete('api/dashboard/admin/Companies/' + self.$route.params.id).then(function () {
                self.$notify({
                    title: self.$i18n.t('Success').toString(),
                    text: self.$i18n.t('Data deleted successfully').toString(),
                    type: 'success'
                });
                self.$router.push('/dashboard/admin/Companies');
            }).catch(function () {
                self.deleteLabelModal = false;
            });
        },
        selectAgent(user) {
            if (this.Companies.agents.includes(user)) {
                for (let i = 0; i < this.Companies.agents.length; i++) {
                    if (this.Companies.agents[i] === user) {
                        this.Companies.agents.splice(i, 1);
                    }
                }
            } else {
                this.Companies.agents.push(user);
            }
        }
    }
}
</script>
