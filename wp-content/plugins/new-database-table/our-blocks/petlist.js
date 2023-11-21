wp.blocks.registerBlockType('ourdatabaseplugin/petlist',{
    title: "Fiction University Pets List",
    edit: function (){
        return wp.element.createElement('div', {className: "our-placeholder-block"},"Pets List Placeholder")
    },
    save: ()=>{
        return null;
    }
});