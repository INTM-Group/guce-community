module.exports = {
  root: true,

  env: {
    node: true,
  },
  extends: ['plugin:vue/essential'],
  rules: {
    'no-console': 'off',
    'no-debugger': 'off',
    'no-unused-vars': 1,
    'vue/max-attributes-per-line': 'off'
  },
  parserOptions: {
    parser: 'babel-eslint',
  }
}
