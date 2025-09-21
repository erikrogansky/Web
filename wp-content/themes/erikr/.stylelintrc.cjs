module.exports = {
    extends: ["stylelint-config-standard-scss"],
    plugins: ["stylelint-scss", "stylelint-order"],
    rules: {
      "color-no-invalid-hex": true,
      "declaration-block-no-duplicate-properties": true,
      "no-duplicate-selectors": true,
      "selector-max-id": 1,
      "max-nesting-depth": 10,
      "no-descending-specificity": null,
      "order/properties-order": [
        [
          "content",
          "position",
          "top",
          "right",
          "bottom",
          "left",
          "z-index",
          "display",
          "flex",
          "grid",
          "align-items",
          "justify-content",
          "width",
          "height",
          "margin",
          "padding",
          "border",
          "background",
          "color",
          "font",
          "text-align",
          "opacity",
          "visibility",
          "overflow"
        ],
        { unspecified: "bottomAlphabetical" }
      ],
      "scss/at-rule-no-unknown": true,
      "scss/dollar-variable-pattern": "^([a-z0-9]+(-[a-z0-9]+)*)$",
      "no-empty-source": null,
      "rule-empty-line-before": ["always", { except: ["first-nested"] }],
      "declaration-empty-line-before": "never",
      "selector-class-pattern": [
        "^[a-z0-9]+(?:-[a-z0-9]+)*(?:__(?:[a-z0-9]+(?:-[a-z0-9]+)*))?(?:--(?:[a-z0-9]+(?:-[a-z0-9]+)*))?$",
        {
          "message": "Expected class selector to be kebab-case or BEM (block__element--modifier)"
        }
      ],
    }
  };