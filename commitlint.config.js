module.exports = {
    extends: ['@commitlint/config-conventional'],
    rules: {
        'type-enum': [
            2,
            'always',
            [
                'build',
                'chore',
                'ci',
                'docs',
                'feat',
                'fix',
                'perf',
                'refactor',
                'revert',
                'style',
                'test'
            ]
        ],
        'subject-case': [2, 'never', ['pascal-case', 'upper-case']],
        'subject-max-length': [2, 'always', 100],
        'body-max-line-length': [2, 'always', 100],
        'footer-max-line-length': [2, 'always', 100]
    }
}
