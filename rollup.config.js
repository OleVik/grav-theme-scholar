import resolve from "@rollup/plugin-node-resolve";
import babel from "@rollup/plugin-babel";
import commonjs from "@rollup/plugin-commonjs";
import { terser } from "rollup-plugin-terser";

const production = !process.env.ROLLUP_WATCH;

export default {
  input: "src/js/theme.js",
  output: {
    file: "js/theme.js",
    format: "umd",
    name: "Scholar",
    sourcemap: !production && true,
  },
  plugins: [
    resolve(),
    babel({
      babelHelpers: "bundled",
      exclude: [/core-js/],
      presets: [
        [
          "@babel/preset-env",
          {
            targets: "defaults",
            useBuiltIns: "usage",
            corejs: {
              version: 3,
              proposals: true,
            },
            modules: false,
          },
        ],
      ],
      babelrc: false,
    }),
    commonjs(),
    production && terser(),
  ],
};
