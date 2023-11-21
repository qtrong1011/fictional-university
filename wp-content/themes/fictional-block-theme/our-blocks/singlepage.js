wp.blocks.registerBlockType("ourblocktheme/singlepage", {
    title: "Fictional University Single Page",
    edit: function () {
      return wp.element.createElement("div", { className: "our-placeholder-block" }, "Single Page Placeholder")
    },
    save: function () {
      return null
    }
  })
  