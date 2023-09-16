<template>
    <div class="hs-accordion-group bg-white rounded-lg shadow-lg px-4 py-2">
      <div class="hs-accordion active" id="hs-basic-with-title-and-arrow-stretched-heading-one">
        <button class="hs-accordion-toggle hs-accordion-active:text-blue-600 group py-2 inline-flex items-center justify-between gap-x-3 w-full font-base text-left text-gray-800 transition hover:text-gray-500 dark:hs-accordion-active:text-blue-500 dark:text-gray-200 dark:hover:text-gray-400" aria-controls="hs-basic-with-title-and-arrow-stretched-collapse-one">
          <div class="flex items-center space-x-6">
            <img :src="$page.props.auth.user.profile_photo_url" class="w-12 h-12 p-[3px] rounded-full ring-1 ring-blue-600 dark:ring-gray-500" alt="">
            <span>เขียนโพสต์ใหม่</span>
          </div>
          <svg class="hs-accordion-active:hidden hs-accordion-active:text-blue-600 hs-accordion-active:group-hover:text-blue-600 block w-3 h-3 text-gray-600 group-hover:text-gray-500 dark:text-gray-400" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M2 5L8.16086 10.6869C8.35239 10.8637 8.64761 10.8637 8.83914 10.6869L15 5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
          <svg class="hs-accordion-active:block hs-accordion-active:text-blue-600 hs-accordion-active:group-hover:text-blue-600 hidden w-3 h-3 text-gray-600 group-hover:text-gray-500 dark:text-gray-400" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M2 11L8.16086 5.31305C8.35239 5.13625 8.64761 5.13625 8.83914 5.31305L15 11" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </button>
        <div id="hs-basic-with-title-and-arrow-stretched-collapse-one" class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300" aria-labelledby="hs-basic-with-title-and-arrow-stretched-heading-one">
          <div class="py-2 px-1">
            <form @submit.prevent="postForm.post('/posts')" id="newPostForm">
              <div class="border-b border-secondary-200 dark:border-secondary-800">                          
                  <textarea id="post-message" name="postcontent" v-model="postForm.content" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Write your thoughts here..."></textarea>
              </div>
              <div class="mt-4 mx-1 flex items-center justify-between">
                <div class="flex">
                  <div class="flex gap-2 px-2 py-1 rounded bg-gray-100 text-gray-400 text-sm dark:bg-dark-strip dark:text-white">
                      <span class="text-sm">Photo</span>
                      <svg class="w-[1rem]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path
                              d="M21.9999 14.7024V16.0859C21.9999 16.3155 21.9899 16.5471 21.9699 16.7767C21.6893 19.9357 19.4949 22 16.3286 22H7.67126C6.06806 22 4.71535 21.4797 3.74341 20.5363C3.36265 20.1864 3.042 19.7753 2.7915 19.3041C3.12217 18.9021 3.49291 18.462 3.85363 18.0208C4.46485 17.289 5.05603 16.5661 5.42677 16.0959C5.97788 15.4142 7.43078 13.6196 9.44481 14.4617C9.85563 14.6322 10.2164 14.8728 10.547 15.0833C11.3586 15.6247 11.6993 15.7851 12.2705 15.4743C12.9017 15.1335 13.3125 14.4617 13.7434 13.76C13.9739 13.388 14.2043 13.0281 14.4548 12.6972C15.547 11.2736 17.2304 10.8926 18.6332 11.7348C19.3346 12.1559 19.9358 12.6872 20.4969 13.2276C20.6172 13.3479 20.7374 13.4592 20.8476 13.5695C20.9979 13.7198 21.4989 14.2211 21.9999 14.7024Z"
                              fill="currentColor"></path>
                          <path opacity="0.4"
                              d="M16.3387 2H7.67134C4.27455 2 2 4.37607 2 7.91411V16.086C2 17.3181 2.28056 18.4119 2.79158 19.3042C3.12224 18.9022 3.49299 18.4621 3.85371 18.0199C4.46493 17.2891 5.05611 16.5662 5.42685 16.096C5.97796 15.4143 7.43086 13.6197 9.44489 14.4618C9.85571 14.6323 10.2164 14.8729 10.5471 15.0834C11.3587 15.6248 11.6994 15.7852 12.2705 15.4734C12.9018 15.1336 13.3126 14.4618 13.7435 13.759C13.9739 13.3881 14.2044 13.0282 14.4549 12.6973C15.5471 11.2737 17.2305 10.8927 18.6333 11.7349C19.3347 12.1559 19.9359 12.6873 20.497 13.2277C20.6172 13.348 20.7375 13.4593 20.8477 13.5696C20.998 13.7189 21.499 14.2202 22 14.7025V7.91411C22 4.37607 19.7255 2 16.3387 2Z"
                              fill="currentColor"></path>
                          <path
                              d="M11.4543 8.79668C11.4543 10.2053 10.2809 11.3783 8.87313 11.3783C7.46632 11.3783 6.29297 10.2053 6.29297 8.79668C6.29297 7.38909 7.46632 6.21509 8.87313 6.21509C10.2809 6.21509 11.4543 7.38909 11.4543 8.79668Z"
                              fill="currentColor"></path>
                      </svg>
                  </div>
                  <div class="flex gap-2 px-2 py-1 mx-4 rounded bg-gray-100 text-gray-400 text-sm dark:bg-dark-strip dark:text-white">
                      <span class="text-sm ">Tag Friend</span>
                      <svg class="w-[1rem]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path
                              d="M11.997 15.1746C7.684 15.1746 4 15.8546 4 18.5746C4 21.2956 7.661 21.9996 11.997 21.9996C16.31 21.9996 19.994 21.3206 19.994 18.5996C19.994 15.8786 16.334 15.1746 11.997 15.1746Z"
                              fill="currentColor"></path>
                          <path opacity="0.4"
                              d="M11.9971 12.5838C14.9351 12.5838 17.2891 10.2288 17.2891 7.29176C17.2891 4.35476 14.9351 1.99976 11.9971 1.99976C9.06008 1.99976 6.70508 4.35476 6.70508 7.29176C6.70508 10.2288 9.06008 12.5838 11.9971 12.5838Z"
                              fill="currentColor"></path>
                      </svg>
                  </div>
                  <div class="px-2 py-1 rounded bg-gray-100 text-sm text-secondary-600 dark:bg-dark-strip dark:text-white">
                      More...
                  </div>
                </div>
                <div class="flex space-x-2">
                  <button type="button" class="px-2 py-1 rounded-lg bg-gray-100 border border-gray-500 text-sm text-gray-500 dark:text-white hover:scale-110">ยกเลิก</button>
                  <button type="submit" class="px-2 py-1 rounded-lg bg-blue-100 border border-blue-500 text-sm text-blue-500 dark:text-white hover:scale-110">เขียนกระดาน</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    
    </div>
</template>
<script setup>
import { useForm } from "@inertiajs/vue3";

const postForm = useForm({
  title: 'title',
  content: 'content',
  point:1
});

</script>
    