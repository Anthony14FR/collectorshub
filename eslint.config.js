import vuePlugin from 'eslint-plugin-vue';
import * as vueParser from 'vue-eslint-parser';
import tseslint from '@typescript-eslint/eslint-plugin';
import tsparser from '@typescript-eslint/parser';

export default [
  {
    files: ['resources/js/**/*.js'],
    rules: {
      'indent': ['error', 2],
      'no-console': 'warn'
    }
  },
  {
    files: ['resources/js/**/*.ts'],
    languageOptions: {
      parser: tsparser,
      parserOptions: {
        ecmaVersion: 'latest',
        sourceType: 'module'
      }
    },
    plugins: {
      '@typescript-eslint': tseslint
    },
    rules: {
      'indent': ['error', 2],
      'no-console': 'warn'
    }
  },
  {
    files: ['resources/js/**/*.vue'],
    languageOptions: {
      parser: vueParser,
      parserOptions: {
        parser: tsparser,
        ecmaVersion: 'latest',
        sourceType: 'module',
        extraFileExtensions: ['.vue']
      }
    },
    plugins: {
      vue: vuePlugin,
      '@typescript-eslint': tseslint
    },
    rules: {
      'indent': ['error', 2],
      'vue/html-indent': ['error', 2],
      'vue/script-indent': ['error', 2],
      'vue/no-unused-components': 'warn',
      'no-console': 'warn',
    }
  }
]; 