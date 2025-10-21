module.exports = {
    root: true,
    env: {
        node: true,
        es2022: true,
    },
    extends: [
        'eslint:recommended',
        '@vue/eslint-config-prettier',
        'plugin:vue/vue3-recommended',
    ],
    parserOptions: {
        ecmaVersion: 'latest',
        sourceType: 'module',
    },
    rules: {
        // Allow shorthand syntax for v-bind when key and value are the same
        'vue/valid-v-bind': 'off',
        
        // Other Vue 3 rules
        'vue/multi-word-component-names': 'off',
        'vue/no-unused-components': 'warn',
        'vue/no-unused-vars': 'warn',
        
        // General rules
        'no-console': 'warn',
        'no-debugger': 'warn',
    },
};