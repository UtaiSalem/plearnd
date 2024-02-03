<script setup>
import { Link } from '@inertiajs/vue3';
import SVGJoinGroup from '@/Components/SVGIcons/Join Group Icon.svg';
import SVGLeaveGroup from '@/Components/SVGIcons/Leave Group Icon.svg';
import Swal from 'sweetalert2';

const props = defineProps({
    academy:Object
});

async function onRequestToBeAMember(){
    try {
        let memberResp = await axios.post(`/academies/${props.academy.id}/members`);
        if (memberResp.data && memberResp.data.success) {
            // console.log(memberResp.data);
            props.academy.memberStatus = memberResp.data.memberStatus;
            if(memberResp.data.memberStatus === 2){props.academy.total_students++};
            Swal.fire(
                'เสร็จสมบูรณ์',
                    memberResp.data.memberStatus ===1 ? 'ขอเข้าร่วมเรียบร้อยแล้ว รอการตอบรับ':'เป็นสมาชิกเรียบร้อยแล้ว' ,
                'success'
            )
        }else if(memberResp.data && !memberResp.data.success) {
            Swal.fire(
                'เกิดข้อผิดพลาด',
                    memberResp.data.msg ,
                'error'
            )
        }
    } catch (error) {
        console.log(error);
    }
}

async function onRequestToBeUnmember(msg){
    try {
        let memberResp = await axios.post(`/academies/${props.academy.id}/unmembers`);
        if (memberResp.data && memberResp.data.success) {
            // console.log(memberResp.data);
            if (props.academy.memberStatus==2) {
                props.academy.total_students--;
            }
            props.academy.memberStatus = null;
            Swal.fire(
                'เสร็จสมบูรณ์',
                msg ,
                'warning'
            )
        }
    } catch (error) {
        console.log(error);
    }
}

</script>

<template>
    <div>
        <div class="rounded-lg bg-white shadow-xl overflow-hidden">
            <figure class="user-preview-cover liquid bg-cover bg-no-repeat bg-center">
                <img :src="academy.cover? 'storage/images/academies/covers/'+academy.cover : 'storage/images/academies/covers/default_cover.png'" alt="cover-46">
            </figure>
            <div class="user-preview-info p-2">
                <div class="flex flex-col justify-center">
                    <Link class=" flex justify-center -mt-12 h-24 mb-2" :href="'/academies/'+academy.id">
                    <div class="-mt-4">
                        <img :src="academy.logo? 'storage/images/academies/logos/'+academy.logo : 'storage/images/academies/logos/default_logo.png'"
                            alt="" class="w-28 h-28 border-2 rounded-full">
                    </div>
                    </Link>
                    <p class="font-bold text-xl flex my-2 justify-center text-center">
                        <Link :href="'/academies/'+academy.id" class="font-mali">{{ academy.name }}</Link>
                    </p>
                    <p class="flex justify-center text-center w-full p-2">
                        <Link :href="'/academies/'+academy.id" class="">
                         <span>{{ academy.slogan }} </span> 
                        </Link>
                    </p>
                </div>

                <div class=" my-1">
                    <div class="p-2">
                        <div class="flex flex-col items-center space-x-2">
                            <a class=" small" href="#">
                                <div class="user-avatar-content">
                                    <img :src="academy.director? academy.director.avatar : academy.creater.avatar" alt="" class="rounded-full w-12">
                                </div>
                            </a>
                            <div class="flex flex-col justify-center items-center">
                                <p class="user-short-description-title"><a href="#">{{ academy.director? academy.director.name : academy.creater.name }}</a></p>
                                <p class="user-short-description-text">{{ academy.director? 'Director' : 'Creater' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-center">
                    <div class="flex flex-col items-center justify-center">
                        <p class="user-stat-title">{{ academy.total_students }}</p>
                        <p class="user-stat-text">สมาชิก</p>
                    </div>
                    <!-- /USER STAT -->

                    <!-- USER STAT -->
                    <!-- <div class="user-stat">
                        <p class="user-stat-title">101</p>
                        <p class="user-stat-text">posts</p>
                    </div> -->
                    <!-- /USER STAT -->

                    <!-- USER STAT -->
                    <!-- <div class="user-stat">
                        <p class="user-stat-title">2.4k</p>
                        <p class="user-stat-text">visits</p>
                    </div> -->
                    <!-- /USER STAT -->
                </div>
                <!-- /USER STATS -->

                <!-- USER PREVIEW ACTIONS -->
                <div class="user-preview-actions" v-if="$page.props.auth.user.id != props.academy.creater.id">
                    <!-- BUTTON -->
                    <button v-if="academy.memberStatus == 1" 
                        class="plearnd-btn-primary flex justify-center"
                        @click.prevent="onRequestToBeUnmember('ยกเลิกคำขอเป็นสมาชิกเรียบร้อยแล้ว')"
                    >
                        <SVGLeaveGroup class=" inline-flex mx-2" />รอการตอบรับสมาชิก
                    </button>
                    <button v-else-if="academy.memberStatus == 2" 
                        class="plearnd-btn-primary flex justify-center" 
                        @click.prevent="onRequestToBeUnmember('ออกจากการเป็นสมาชิกเรียบร้อยแล้ว')"    
                    >
                        <SVGLeaveGroup class=" inline-flex mx-2" />เลิกเป็นสมาชิก
                    </button>
                    <button v-else class="plearnd-btn-primary flex justify-center" @click.prevent="onRequestToBeAMember">
                        <SVGJoinGroup class=" inline-flex mx-2" />ขอเป็นสมาชิก
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
