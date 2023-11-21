import './frontend.scss'
import React, {useState, useEffect} from 'react'
import ReactDOM from 'react-dom'


const divsToUpdate = document.querySelectorAll('.paying-attention-update-me');

divsToUpdate.forEach((div)=>{
    const data = JSON.parse(div.querySelector("pre").innerHTML)
    ReactDOM.render(<Quiz {...data} />, div)
    div.classList.remove('paying-attention-update-me')
})

function Quiz(props) {
    const [isCorrect, setIsCorrect] = useState(undefined)
    const [isCorrectDelay, setIsCorrectDelay] = useState(undefined)
    useEffect(()=>{
        if(isCorrect === false){
            setTimeout(()=>{
                setIsCorrect(undefined)
            },2600)
        }
        if(isCorrect === true){
            setTimeout(()=>{
                setIsCorrectDelay(true)
            },1000)
        }
    },[isCorrect])
    function handleAnswer(index){
        if(index == props.correctAnswer){
            setIsCorrect(true)

        }else{
            setIsCorrect(false)

        }
    }
    return (
        <div className="paying-attention-frontend" style={{backgroundColor: props.bgColor, textAlign: props.theAlignment}}>
            <p>{props.question}</p>
            <ul>
                {props.answers.map((answer,index)=>{
                    return (
                        <li className={(isCorrectDelay===true && index == props.correctAnswer ? "no-click" : "") + (isCorrectDelay === true && index != props.correctAnswer ? "fade-incorrect":"")} onClick={isCorrect === true ? undefined : ()=>handleAnswer(index)}>
                            {isCorrectDelay === true && index == props.correctAnswer && (
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" className="bi bi-check-lg" viewBox="0 0 16 16">
                                <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z"/>
                              </svg>
                            )}
                            {isCorrectDelay === true && index !== props.correctAnswer && (
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" className="bi bi-x-lg" viewBox="0 0 16 16">
                                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z"/>
                              </svg>
                            )}
                            {answer}
                        </li>
                        
                    )
                })}
            </ul>
            <div className={"correct-message" + (isCorrect === true ? " correct-message--visible" : "")}>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"  className="bi bi-emoji-smile" viewBox="0 0 16 16">
  <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
  <path d="M4.285 9.567a.5.5 0 0 1 .683.183A3.498 3.498 0 0 0 8 11.5a3.498 3.498 0 0 0 3.032-1.75.5.5 0 1 1 .866.5A4.498 4.498 0 0 1 8 12.5a4.498 4.498 0 0 1-3.898-2.25.5.5 0 0 1 .183-.683zM7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5zm4 0c0 .828-.448 1.5-1 1.5s-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5z"/>
</svg>
                <p>That is correct.</p>
            </div>
            <div className={"incorrect-message" + (isCorrect === false ? " incorrect-message--visible" : "")}>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"  className="bi bi-emoji-frown" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                <path d="M4.285 12.433a.5.5 0 0 0 .683-.183A3.498 3.498 0 0 1 8 10.5c1.295 0 2.426.703 3.032 1.75a.5.5 0 0 0 .866-.5A4.498 4.498 0 0 0 8 9.5a4.5 4.5 0 0 0-3.898 2.25.5.5 0 0 0 .183.683zM7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5zm4 0c0 .828-.448 1.5-1 1.5s-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5z"/>
            </svg>
                <p>Sorry. Try again.</p>
            </div>
        </div>
    )
}
