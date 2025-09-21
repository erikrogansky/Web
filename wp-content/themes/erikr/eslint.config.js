import js from "@eslint/js";
import ts from "@typescript-eslint/eslint-plugin";
import tsParser from "@typescript-eslint/parser";
import prettier from "eslint-plugin-prettier";

export default [
  js.configs.recommended,
  {
    files: ["**/*.ts", "**/*.tsx"],
    languageOptions: {
      parser: tsParser,
      globals: {
        console: "readonly"
      }
    },
    plugins: {
      "@typescript-eslint": ts,
      prettier: prettier
    },
    rules: {
      "@typescript-eslint/no-unused-vars": ["error", { argsIgnorePattern: "^_" }],
      "@typescript-eslint/no-explicit-any": "error",
      "prettier/prettier": ["error", { tabWidth: 4 }],
      "no-undef": "off",
      "no-console": "off"
    }
  }
];
