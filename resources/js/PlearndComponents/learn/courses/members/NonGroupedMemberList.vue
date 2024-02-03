<script setup>
import { ref, computed } from 'vue';
import { usePage } from "@inertiajs/vue3";
import { Icon } from '@iconify/vue';
import Badge from '@/PlearndComponents/accessories/Badge.vue';
import CyanSeaButton from '@/PlearndComponents/accessories/CyanSeaButton.vue';
import MemberCard from '@/PlearndComponents/learn/courses/members/MemberCard.vue'
import StaggeredFade from '@/PlearndComponents/accessories/StaggeredFade.vue';

const props = defineProps({
    members: {
        type: Object,
        default: () => ({})
    },
    groups: {
        type: Object,
        default: () => ({})
    },
});

const activeGroupTab = ref(0);
const emit = defineEmits(['request-unmember-course']);

const activeGroupMembers = computed(()=> {
    if (activeGroupTab.value < props.groups.length) {
        return props.groups[activeGroupTab.value].members;
    }else{
        return props.members.filter((member)=> !member.group);
    }
});


function requestToBeUnMember() {
    emit('request-unmember-course');
}

function setActiveGroupTab(tab){
    activeGroupTab.value = tab;
    // console.log(props.groups[tab].id);
    // console.log(tab);
}
</script>

<template>
    <div class="w-full mx-auto ">
        <section class="max-w-full" aria-multiselectable="false">
            <div v-if="props.members.length" class="flex items-center justify-center bg-white border-t-4 border-blue-600 rounded-lg overflow-hidden shadow-lg">
                <div class="tabs flex flex-col justify-center p-4">
                    <div class="tabs-header px-4 w-full flex flex-col items-center justify-center">
                        <div  v-if="props.members.length" class="text-xl font-medium">
                            รอการตอบรับเป็นสมาชิก ( {{ props.members.length }} ) คน
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="flex items-center justify-center bg-white border-t-4 border-blue-600 rounded-lg overflow-hidden shadow-lg">
                <div class="tabs flex flex-col justify-center p-4">
                    <div class="tabs-header px-4 w-full flex flex-col items-center justify-center">
                        <div class="text-base font-normal">
                            (ไม่มีสมาชิกรอการตอบรับ)
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <div class=" pb-4" id="tab-panel-1a" aria-hidden="false" role="tabpanel" aria-labelledby="tab-label-1a"
                    tabindex="-1">
                    <staggered-fade :duration="50" tag="ul" class="flex flex-col w-full ">
                        <MemberCard v-for="(member, index) in props.members" :key="index"
                            :data-index="index" 
                            :member="member"
                            @request-unmember-course="onRequestToBeUnMember(props.courseMemberOfAuth.id, index)" />
                    </staggered-fade>
                            <!-- :groups="props.groups"  -->
                </div>
            </div>
        </section>
    </div>
</template>