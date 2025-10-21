import js from '@eslint/js';

export default [
    js.configs.recommended,
    {
        rules: {
            // General rules
            'no-console': 'warn',
            'no-debugger': 'warn',
        }
    }
];