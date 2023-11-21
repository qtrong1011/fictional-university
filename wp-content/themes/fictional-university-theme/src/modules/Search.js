import $ from 'jquery'

class Search {
    // 1. Describe and initiate our object
    constructor(){
        this.addSearchHTML();
        this.resultsDiv = $("#search-overlay__results");
        this.openButton = $(".js-search-trigger");
        this.closeButton = $(".search-overlay__close");
        this.searchOverlay = $(".search-overlay");
        this.searchField = $("#search-term");
        this.events();
        this.isOverlayOpen = false;
        this.isSpinnerVisible = false;
        this.previousValue;
        this.typingTimer;
    }
    // 2. events
    events(){
        //click on the search icon to open search overlay section
        this.openButton.on("click", this.openOverlay.bind(this));
        //click on the 'x' icon to close search overlay section
        this.closeButton.on("click",this.closeOverlay.bind(this));
        // press 's' or 'esc' keys to open or close overlay section, respectively
        $(document).on('keydown',this.keyPressDispatcher.bind(this));
        // enter searchField
        this.searchField.on('keyup',this.typingLogic.bind(this));

    }

    // 3. methods (fuctions, actions...)
    // to open search overlay section
    openOverlay(){
        this.searchOverlay.addClass("search-overlay--active");
        $("body").addClass("body-no-scroll");
        this.searchField.val('');
        setTimeout(()=>this.searchField.trigger('focus'),301);
        
        console.log("our open method just ran!");
        this.isOverlayOpen = true;
        return false;
    }
    //to close search overlay section
    closeOverlay(){
        this.searchOverlay.removeClass("search-overlay--active");
        $("body").removeClass("body-no-scroll");
        console.log("our close method just ran!")
        this.isOverlayOpen = false;
    }
    //to open or close search overlay secion when keys are pressed.
    keyPressDispatcher(e){
        // "s" on the keyboard
        if(e.keyCode == 83 && !this.isOverlayOpen && !$("input, textarea").is(':focus')){
            this.openOverlay();
        }
        // 'esc' on the keyboard
        else if(e.keyCode == 27 && this.isOverlayOpen){
            this.closeOverlay();
        }
    }
    // Logic of typing
    // 1: Try to truly timing when user begins to search
    // 2: Loading icons should appear at the right time
    typingLogic(){
        //Check to see if the search value is being changed to appear loading icon at the right tim
        if(this.searchField.val() != this.previousValue){
            clearTimeout(this.typingTimer); //reset timing to get truly timing for user
            // Check to see if the search value is empty or not
            if(this.searchField.val()){
                if(!this.isSpinnerVisible){
                    this.resultsDiv.html('<div class="spinner-loader"></div>');
                    this.isSpinnerVisible = true;
                }
                this.typingTimer = setTimeout(this.getResults.bind(this),750);
            }else  
            {
                this.resultsDiv.html('');
                this.isSpinnerVisible = false;
            }
            
        }
        
        this.previousValue = this.searchField.val();
    }
    //
    getResults(){

        $.getJSON(universityData.root_url + '/wp-json/university/v1/search?term=' + this.searchField.val(),(results)=>{
            this.resultsDiv.html(
                `
                    <div class="row">
                        <div class="one-third">
                            <h2 class="search-overlay__section-title">General Information</h2>
                            ${results.generalInfo.length ? '<ul class="link-list min-list">' : '<p>No results found.</p>'}
                            ${
                                results.generalInfo.map((result) =>
                                    `<li><a href="${result.url}">${result.title}</a> ${result.post_type == 'post' ? `by ${result.author_name}` : result.post_type == 'page' ? `by ${result.author_name}`:''}</li>`
                            ).join('')
                            }
                            ${results.generalInfo.length ? '</ul>':''}
                        </div>
                        <div class="one-third">
                            <h2 class="search-overlay__section-title">Programs</h2>
                            ${results.programs.length ? '<ul class="link-list min-list">' : `<p>No programs match that search.<a href="${universityData.root_url}/programs">View all programs</a></p>`}
                            ${
                                results.programs.map((result) =>
                                    `<li><a href="${result.url}">${result.title}</a></li>`
                            ).join('')
                            }
                            ${results.programs.length ? '</ul>':''}
                            
                            <h2 class="search-overlay__section-title">Professors</h2>
                            ${results.professors.length ? '<ul class="professor-cards">' : `<p>No professors match that search.</p>`}
                            ${
                                results.professors.map((result) =>
                                    `<li class="professor-card__list-item">
                                    <a class="professor-card" href="${result.url}">
                                    <img class="professor-card__image" src="${result.image}">
                                    <span class="professor-card__name">${result.title}</span>
                                    </a>
                                </li>`
                            ).join('')
                            }
                            ${results.professors.length ? '</ul>': ''}
                            
                        
                        </div>
                        <div class="one-third">
                            <h2 class="search-overlay__section-title">Events</h2>
                            ${results.events.length ? '' : `<p>No events match that search. <a href="${universityData.root_url}/events">View all upcoming events</a></p>`}
                            ${
                                results.events.map((result) =>
                                    `
                                    <div class="event-summary">
                                    <a class="event-summary__date t-center" href="${result.url} ?>">
                                    <span class="event-summary__month">${result.month}</span>
                                    <span class="event-summary__day">${result.day}</span>
                                    </a>
                                  <div class="event-summary__content">
                                    <h5 class="event-summary__title headline headline--tiny"><a href="${result.url}">${result.title}</a></h5>
                                    <p>${result.description} <a href="${result.url}" class="nu gray">Learn more</a></p>
                                  </div>
                                </div>
                                    `
                            ).join('')
                            }
                            ${results.events.length ? '</ul>': ''}
                        
                        </div>
                    </div>
                `
            );
            this.isSpinnerVisible = false;


        })
    }
    //
    addSearchHTML(){
        $("body").append(`
            <div class="search-overlay">
                <div class="search-overlay__top">
                <div class="container">
                    <i class="fa fa-search search-overlay__icon" aria-hidden="true"></i>
                    <input type="text" class="search-term" placeholder="What are you looking for?" id="search-term" autocomplete="off">
                    <i class="fa fa-window-close search-overlay__close" aria-hidden="true"></i>
        
                </div>
                <div class="container">
                    <div id="search-overlay__results">
                    
                    </div>
        
                </div>
        
                </div>
            
            </div>
        `);
    }
}

export default Search