import {TextControl, Flex, FlexBlock, FlexItem, Button, Icon, PanelBody, PanelRow, ColorPicker} from '@wordpress/components'
import {InspectorControls, BlockControls, AlignmentToolbar, useBlockProps} from '@wordpress/block-editor'
import "./index.scss"
import {ChromePicker} from 'react-color'

// Immediately invoke function expression (IIFE funtion)
(function (){
    let locked = false
    wp.data.subscribe(()=>{
        const results = wp.data.select('core/block-editor').getBlocks().filter(function(block){
            return block.name == 'ourplugin/are-you-paying-attention' && block.attributes.correctAnswer == undefined
        })
        if (results.length && locked == false){
            locked = true
            wp.data.dispatch('core/editor').lockPostSaving('noanswer')
        }
        if(!results.length && locked == true){
            locked = false
            wp.data.dispatch('core/editor').unlockPostSaving('noanswer')
        }
    })
}) ()
wp.blocks.registerBlockType('ourplugin/are-you-paying-attention',{
    title: "Are You Paying Attention?",
    icon: "smiley",
    category: "common",
    attributes:{
        question: {type:"string"},
        answers: {type:"array", default: [""]},
        correctAnswer: {type:"number", default: undefined},
        bgColor : {type:"String", default:"#EBEBEB"},
        theAlignment: {type:"string", default:"left"}
    },
    example: {
        attributes: {
            question: "What is your name",
            answers: ['Jason', 'Trong','Kate','Hue'],
            correctAnswer: 0,
            bgColor : "#EBEBEB",
            theAlignment: "center"
        }
    },
    edit: EditComponent,
    save: (props)=>{
        return null;
    }
});

function EditComponent(props) {
        const blockProps = useBlockProps({
            className: 'paying-attention-edit-block', 
            style:{backgroundColor: props.attributes.bgColor}
        })
        function updateQuestion(value){
            props.setAttributes({question: value })
        }
        function markAsCorrect(indexToMark){
            props.setAttributes({correctAnswer:indexToMark})
        }
        return(
            <div {...blockProps} >
                <BlockControls>
                    <AlignmentToolbar value={props.attributes.theAlignment} onChange={(e)=> props.setAttributes({theAlignment:e})}/>
                </BlockControls>
                <InspectorControls>
                    <PanelBody title='Background Color' initialOpen={true}>
                        <PanelRow>
                            <ChromePicker color={props.attributes.bgColor} onChangeComplete={(e)=> props.setAttributes({bgColor: e.hex})} disableAlpha={true}/>
                        </PanelRow>
                    </PanelBody>
                </InspectorControls>
                <TextControl label="Question: " style={{fontSize : "20px"}} value={props.attributes.question} onChange={updateQuestion} />
                <p style={{fontSize: "13px",margin:"20px 0px 8px 0px"}}>Answers:</p>
                {props.attributes.answers.map((answer,index)=>{
                    return (
                        <Flex>
                            {/* Answer input */}
                            <FlexBlock>
                                <TextControl value={answer} autoFocus={answer == undefined} onChange={(value)=>{
                                    const newAnswers = props.attributes.answers.concat([])
                                    newAnswers[index] = value
                                    props.setAttributes({answers: newAnswers})

                                }}/>
                            </FlexBlock>
                            {/* Star Icon */}
                            <FlexItem>
                                <Button onClick={()=> markAsCorrect(index)}>
                                    <Icon className='mark-as-correct' icon={props.attributes.correctAnswer == index ? 'star-filled' : 'star-empty'}></Icon>
                                </Button>
                            </FlexItem>
                            {/* Delete Button */}
                            <FlexItem>
                                <Button isLink className='delete-answer' onClick={()=>{
                                    const newAnswers = props.attributes.answers.filter((x,indexNewAnswers)=>{
                                        return indexNewAnswers != index
                                    })
                                    props.setAttributes({answers: newAnswers})
                                    if(index == props.attributes.correctAnswer){
                                        props.setAttributes({correctAnswer: undefined})
                                    }
                                }}>Delete</Button>
                            </FlexItem>
                        </Flex>
                    )

                })}
                <Button isPrimary  onClick={()=>{
                    props.setAttributes({answers: props.attributes.answers.concat([undefined])})
                }}>Add Another Answer</Button>

            </div>
        )
    }