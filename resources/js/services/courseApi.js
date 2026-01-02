/**
 * Course API Service
 * Centralized API calls for Course SPA
 */
import axios from 'axios';

const BASE = '/api/courses';
const BASE_V2 = '/api/v2/courses';

export const courseApi = {
    // Course
    getCourse: (id) => axios.get(`${BASE}/${id}`),
    
    // Tab Data
    getFeeds: (id, page = 1) => axios.get(`/courses/${id}/feeds/get-more-activities?page=${page}`),
    getLessons: (id) => axios.get(`${BASE}/${id}/lessons`),
    getMembers: (id) => axios.get(`${BASE}/${id}/members`),
    getAssignments: (id) => axios.get(`${BASE}/${id}/assignments`),
    getAttendances: (id) => axios.get(`${BASE}/${id}/attendances`),
    getQuizzes: (id) => axios.get(`${BASE}/${id}/quizzes`),
    getGroups: (id) => axios.get(`${BASE_V2}/${id}/groups`),
    getProgress: (id) => axios.get(`${BASE}/${id}/progress`),
};
