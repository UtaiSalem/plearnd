<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});
const showPassword = ref(false);
const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Register" />
        <GuestLayout pageName="สมัครสมาชิก">
            <form @submit.prevent="submit">
                <div>
                    <InputLabel for="name" value="Name" />
                    <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required autofocus
                        placeholder="ชื่อ - สกุล" autocomplete="name" />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="mt-4">
                    <InputLabel for="email" value="Email" />
                    <TextInput id="email" v-model="form.email" type="text" class="mt-1 block w-full" required
                        placeholder="อีเมล" autocomplete="username" />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="mt-4">
                    <InputLabel for="password" value="Password" />
                    <TextInput id="password" v-model="form.password" :type="showPassword ? 'text' : 'password'"
                        class="mt-1 block w-full" required placeholder="รหัสผ่าน" autocomplete="new-password" />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>
                <div class="flex mt-2 space-x-2">
                    <input id="customCheckRegister" type="checkbox" class="bg-gray-400 rounded h-4 w-4 ml-1"
                        style="transition: all 0.15s ease 0s;" @change="showPassword = !showPassword" />
                    <InputLabel for="customCheckRegister" value="Show password" />
                    <!-- <span class="ml-2 text-sm font-semibold text-gray-700">
                            Show password
                        </span> -->

                </div>
                <div class="mt-4">
                    <InputLabel for="password_confirmation" value="Confirm Password" />
                    <TextInput id="password_confirmation" v-model="form.password_confirmation"
                        :type="showPassword ? 'text' : 'password'" class="mt-1 block w-full" required
                        placeholder="ยืนยันรหัสผ่าน" autocomplete="new-password" />
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>


                <div class="flex flex-col items-center justify-end mt-4">
                    <button class="plearnd-btn-primary" type="submit" :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing" style="transition: all 0.15s ease 0s;">
                        สมัครสมาชิก
                    </button>
                </div>
            </form>
            <div class=" text-right mt-4">
                <Link :href="route('login')"
                    class="underline text-sm text-gray-600 font-mali dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                เป็นสมาชิกแล้ว เข้าสู่ระบบ?
                </Link>
            </div>
        </GuestLayout>
</template>

