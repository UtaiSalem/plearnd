import js from '@eslint/js';
import pluginVue from 'eslint-plugin-vue';
import globals from 'globals';

export default [
  js.configs.recommended,
  ...pluginVue.configs['flat/essential'],
  {
    languageOptions: {
      ecmaVersion: 'latest',
      sourceType: 'module',
      globals: {
        ...globals.browser,
        ...globals.node,
        axios: 'readonly',
        Swal: 'readonly',
        $page: 'readonly'
      }
    },
    rules: {
      // Vue specific rules
      'vue/multi-word-component-names': 'off',
      'vue/no-v-html': 'warn',
      'vue/no-mutating-props': 'warn', // Allow prop mutations with warning
      
      // General JavaScript rules - more lenient for existing code
      'no-unused-vars': 'warn',
      'no-console': 'off', // Allow console statements
      'semi': ['warn', 'always'], // Change to warning
      'quotes': ['warn', 'single'], // Change to warning
      'indent': ['warn', 2], // Change to warning
      'comma-dangle': 'off', // Allow trailing commas
      
      // Allow specific patterns for your project
      'no-debugger': 'warn',
      'no-alert': 'off' // Allow Swal alerts
    }
  }
];