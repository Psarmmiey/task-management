<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DialogModal from "@/Components/DialogModal.vue";
import Dropdown from "@/Components/Dropdown.vue";
import Options from "@/Components/Options.vue";
import {reactive, ref, watch} from "vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import {router, useForm} from "@inertiajs/vue3";

const props = defineProps({
    tasks: {
        type: Object,
        required:true
    }
})
const openCreateTask = ref(false);
const openViewTask = ref(false);
const selectedTask = ref(null);
const openChangeStatus = ref(false);

const showCreateTaskModal = () => {
    openCreateTask.value = true;
};

const showViewModal = (task) => {
    openViewTask.value = true;
    selectedTask.value = task;
    editForm.title = task.title;
    editForm.description = task.description;
    editForm.due_date = task.due_date;
    editForm.status = task.status;
};

const closeViewModal = () => {
    openViewTask.value = false;
    //selectedTask.value = null;
};

const form = useForm({
    title: '',
    description: '',
    due_date: '',
    status: 'pending',
});

const editForm = useForm({
    title: '',
    description: '',
    due_date: '',
    status: '',
});

const changeStatusForm = useForm({
    status: '',
});

const showChangeStatusModal = (task) => {
    openChangeStatus.value = true;
    selectedTask.value = task;
    changeStatusForm.status = task.status;
};

const closeChangeStatusModal = () => {
    openChangeStatus.value = false;
};

const closeModal = () => {
    openCreateTask.value = false;
    form.title = '';
    form.description = '';
    form.due_date = '';
    form.error = '';
};

function statusClass(status) {
    return {
        pending: 'bg-yellow-400',
        ongoing: 'bg-blue-500',
        completed: 'bg-green-500',
    }[status];
}

function formatDate(dateString) {
    if (!dateString) return "";

    const dueDate = new Date(dateString);
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);

    const diffInTime = dueDate.getTime() - today.getTime();
    const diffInDays = Math.ceil(diffInTime / (1000 * 3600 * 24));

    if (diffInDays === 0) {
        return "Today";
    } else if (diffInDays === 1) {
        return "Tomorrow";
    } else if (diffInDays > 1 && diffInDays <= 7) { // Up to 7 days ahead
        return `${diffInDays} days`;
    } else {
        const day = String(dueDate.getDate()).padStart(2, "0");
        const month = String(dueDate.getMonth() + 1).padStart(2, "0");
        const year = dueDate.getFullYear();
        return `${day}/${month}/${year}`;
    }
}

const createTask = () => {
    form.post(route('tasks.store'), {
        preserveScroll: true,
        onSuccess: (page) => {
            closeModal();
            tasksState.data = page.props.tasks.data;
        },
    });
};

const updateTask = () => {
    editForm.put(route('tasks.update', selectedTask.value.id), {
        preserveScroll: true,
        onSuccess: (page) => {
            closeViewModal();
            tasksState.data = page.props.tasks.data;

        },
    });
};

const changeStatus = () => {
    changeStatusForm.put(route('tasks.updateStatus', selectedTask.value.id), {
        preserveScroll: true,
        onSuccess: (page) => {
            closeChangeStatusModal();
            tasksState.data = page.props.tasks.data;

        },
    });
};

const deleteTask = (taskId) => {
    if (confirm('Are you sure you want to delete this task?')) {
        form.delete(route('tasks.destroy', taskId), {
            preserveScroll: true,
            onSuccess: (page) => {
                tasksState.data = page.props.tasks.data;
            },
        });
    }
};

const filter = ref('all');
const tasksState = reactive(props.tasks);
watch(filter, (newFilter) => {
    router.reload(
        {
            data: {status: newFilter, tasks: null},
            preserveState: true,
            replace: true,
            onSuccess: (page) => {
                tasksState.data = page.props.tasks.data;
            },
        });
});

const loadMore = () => {
    router.reload({
        data: {tasks: tasksState.meta.next_cursor},
        preserveState: true,
        onSuccess: (page) => {
            tasksState.data.push(...page.props.tasks.data);
            tasksState.meta = page.props.tasks.meta;
        },
    });
};
const loadMoreButton = ref(null);
</script>

<template>
    <AppLayout title="Tasks">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tasks
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-6">
                    <select
                        id="filter"
                        v-model="filter"
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-1/4">
                        <option value="all">All</option>
                        <option value="pending">Pending</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="completed">Completed</option>
                    </select>
                    <PrimaryButton @click="showCreateTaskModal()">Create Task</PrimaryButton>
                </div>
                <ul>
                    <li v-for="task in tasksState.data" :key="task.id" class="mb-4 cursor-pointer">
                        <div class="bg-white p-3 rounded-lg flex justify-between items-center">
                            <div>
                                <a href="javascript:void(0)" @click="showViewModal(task)">
                                    <h3 class="text-lg font-medium mb-4" @click="showViewModal(task)">{{
                                            task.title
                                        }}</h3>
                                </a>
                                <div class="flex items-center">
                                    <p class="text-sm text-gray-500 mr-4">Due {{ formatDate(task.due_date) }}</p>
                                    <div :class="statusClass(task.status)"
                                         class="rounded-full py-1 px-3 text-xs text-white uppercase">
                                        {{ task.status }}
                                    </div>
                                    <!--Overdue-->
                                    <span
                                        v-if="(task.status === 'pending' || task.status === 'ongoing') && new Date(task.due_date) < new Date()"
                                        class="text-xs text-red-500 ml-2">Overdue </span>
                                </div>
                            </div>
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <button class="text-gray-500 hover:text-gray-700 self-center">
                                        <Options/>
                                    </button>
                                </template>

                                <template #content>
                                    <!-- Account Management -->
                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        Manage Task
                                    </div>

                                    <DropdownLink as="button" @click="showChangeStatusModal(task)">
                                        Update Status
                                    </DropdownLink>

                                    <div class="border-t border-gray-200"/>
                                    <DropdownLink as="button" @click="deleteTask(task.id)">
                                        <span class="text-red-600">Delete</span>
                                    </DropdownLink>
                                </template>
                            </Dropdown>

                        </div>
                    </li>
                </ul>

                <!-- Pagination -->
                <div class="mt-4">
                    <PrimaryButton v-if="tasksState.meta.next_cursor" ref="loadMoreButton" @click="loadMore">
                        Load More
                    </PrimaryButton>
                </div>
            </div>
        </div>
        <DialogModal :show="openCreateTask" @close="closeModal">
            <template #title>
                New Task
            </template>

            <template #content>
                <div class="mt-4">
                    <InputLabel for="title" value="Title"/>
                    <TextInput
                        id="title"
                        ref="titleInput"
                        v-model="form.title"
                        class="mt-1 block w-3/4"
                        placeholder="Task Title"
                        type="text"
                    />
                    <InputError :message="form.errors.title" class="mt-2"/>
                </div>

                <div class="mt-4">
                    <InputLabel for="description" value="Description"/>
                    <textarea
                        id="description"
                        ref="descriptionInput"
                        v-model="form.description"
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-3/4"
                        placeholder="Task Description"
                        type="text"
                    />
                    <InputError :message="form.errors.description" class="mt-2"/>
                </div>

                <div class="mt-4">
                    <InputLabel for="due_date" value="Due Date"/>
                    <TextInput
                        id="due_date"
                        ref="dueDateInput"
                        v-model="form.due_date"
                        class="mt-1 block w-3/4"
                        placeholder="Due Date"
                        type="date"
                    />
                    <InputError :message="form.errors.due_date" class="mt-2"/>
                </div>

                <div class="mt-4">
                    <InputLabel for="status" value="Status"/>
                    <select
                        id="status"
                        v-model="form.status"
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-3/4"
                    >
                        <option value="pending">Pending</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="completed">Completed</option>
                    </select>
                    <InputError :message="form.errors.status" class="mt-2"/>
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="closeModal">
                    Cancel
                </SecondaryButton>

                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    class="ms-3"
                    @click="createTask"
                >
                    Save
                </PrimaryButton>
            </template>
        </DialogModal>

        <DialogModal :show="openViewTask" @close="closeViewModal">
            <template #title>
                {{ selectedTask.title }}
            </template>

            <template #content>
                <div class="mt-4">
                    <InputLabel for="title" value="Title"/>
                    <TextInput
                        id="title"
                        ref="titleInput"
                        v-model="editForm.title"
                        class="mt-1 block w-3/4"
                        placeholder="Task Title"
                        type="text"
                    />
                    <InputError :message="editForm.errors.title" class="mt-2"/>
                </div>

                <div class="mt-4">
                    <InputLabel for="description" value="Description"/>
                    <textarea
                        id="description"
                        ref="descriptionInput"
                        v-model="editForm.description"
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-3/4"
                        placeholder="Task Description"
                        type="text"
                    />
                    <InputError :message="editForm.errors.description" class="mt-2"/>
                </div>

                <div class="mt-4">
                    <InputLabel for="due_date" value="Due Date"/>
                    <TextInput
                        id="due_date"
                        ref="dueDateInput"
                        v-model="editForm.due_date"
                        class="mt-1 block w-3/4"
                        placeholder="Due Date"
                        type="date"
                    />
                    <InputError :message="editForm.errors.due_date" class="mt-2"/>
                </div>

                <div class="mt-4">
                    <InputLabel for="status" value="Status"/>
                    <select
                        id="status"
                        v-model="editForm.status"
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-3/4"
                    >
                        <option value="pending">Pending</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="completed">Completed</option>
                    </select>
                    <InputError :message="editForm.errors.status" class="mt-2"/>
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="closeViewModal">
                    Cancel
                </SecondaryButton>

                <PrimaryButton
                    :class="{ 'opacity-25': editForm.processing }"
                    :disabled="editForm.processing"
                    class="ms-3"
                    @click="updateTask"
                >
                    Update
                </PrimaryButton>
            </template>
        </DialogModal>

        <DialogModal :show="openChangeStatus" @close="closeChangeStatusModal">
            <template #title>
                Update Task Status
            </template>

            <template #content>
                <div class="mt-4">
                    <InputLabel for="status" value="Status"/>
                    <select
                        id="status"
                        v-model="changeStatusForm.status"
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-3/4"
                    >
                        <option value="pending">Pending</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="completed">Completed</option>
                    </select>
                    <InputError :message="changeStatusForm.errors.status" class="mt-2"/>
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="closeChangeStatusModal">
                    Cancel
                </SecondaryButton>

                <PrimaryButton
                    :class="{ 'opacity-25': changeStatusForm.processing }"
                    :disabled="changeStatusForm.processing"
                    class="ms-3"
                    @click="changeStatus"
                >
                    Update
                </PrimaryButton>
            </template>

        </DialogModal>

    </AppLayout>
</template>

