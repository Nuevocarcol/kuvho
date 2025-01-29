<!-- {{ url('story') }} -->

<link
    rel="stylesheet"
    type="text/css"
    href="{{ url('assets/css/icofont.css') }}"
/>

<style>
    
    @media screen and (max-width: 425px) {
        .close_icon {
            top: 2rem;
            right: 2rem;
        }
    }
    * {
        margin: 0;
    }
    .story_play_container {
        color: white;
        background-color: black;
        height: 100vh;
        width: 100vw;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
    }
    .sotry_play_image {
        height: 100%;
        width: 100%;
        object-fit: contain;
    }
    .close_icon {
        cursor: pointer;
        padding: 0.3rem;
        background-color: rgba(255, 255, 255, 0.173);
        border-radius: 10rem;
        position: absolute;
        top: 1.5rem;
        right: 1rem;
        z-index: 9999;
        color: white;
        font-size: 2rem;
    }
    .close_icon:hover {
        background-color: rgba(255, 255, 255, 0.133);
    }
    .story_details {
        position: absolute;
        /* background-color: white; */
        top: 1rem;
        /* width: 100%; */
        font-size: 1.1rem;
        padding: 1rem;
    }
    .sotry_play_username {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }
    .sotry_play_profile {
        height: 2.1rem;
        width: 2.1rem;
        border-radius: 10rem;
    }
    .two {
        display: none;
    }
    .bar {
        width: 98vw;
        position: absolute;
        top: 1rem;
        overflow-x: hidden;
        z-index: 2;
    }
        .in {
            animation: fill 9s linear 1;
            height: 0.2rem;
            /* width: 100vw;     */
            background-color: rgb(255, 255, 255);
            margin-inline: 3rem;
            overflow-x: hidden;
        }


    @keyframes fill {
        0% {
            width: 0%;
        }
        100% {
            width: 95%;
        }
    }
    .multiple_bar {
        margin-inline: 4rem;
        z-index: 1;
        width: 90vw;
        height: 0.2rem;
        position: absolute;
        top: 1rem;
        overflow-x: hidden;
        display: flex;
        gap: .6rem;
    }
    .single_bar {
        height: 0.2rem;
        width: 100%;
        background-color: rgba(169, 169, 169, 0.631);
    }
</style>

<div class="bar">
    <div class="in " id="myDIV"></div>
</div>
<div class="multiple_bar" id="multiple_bar">
    <!-- <div class="single_bar"></div> -->
    <!-- <div class="single_bar"></div>
    <div class="single_bar"></div> -->
</div>

<div class="story_play_container">
    <div class="">
        <img
            id="myImg"
            class="sotry_play_image one"
            src="{{ asset('assets/images/story/'.$firts_story->url) }}"
            alt=""
        />
        {{-- <img
            class="sotry_play_image two"
            src="https://media.istockphoto.com/id/517188688/photo/mountain-landscape.jpg?s=612x612&w=0&k=20&c=A63koPKaCyIwQWOTFBRWXj_PwCrR4cEoOw2S9Q7yVl8="
            alt=""
        /> --}}

        <div class="story_details">
            <div class="sotry_play_username">
                <img
                    class="sotry_play_profile"
                    src="https://cdn.pixabay.com/photo/2016/09/07/11/37/sunset-1651426__340.jpg"
                    alt=""
                />
                <div style="font-size: 1.1rem">pawan.1817_</div>
                <div style="opacity: 0.8; font-size: 1rem">7h</div>
            </div>
        </div>
    </div>
</div>

<i class="icofont icofont-close-line close_icon" onclick="close_click()"></i>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script>
    
    let data = [
            @foreach($story_Ar as $story)
        {
            id: "{{ $story['story_id'] }}",
            src: "{{ $story['url'] }}",
        },
            @endforeach
        // {
        //     id: 2,
        //     src: "https://cdn.pixabay.com/photo/2014/02/27/16/10/flowers-276014__340.jpg",
        // },
        // {
        //     id: 3,
        //     src: "https://cdn.pixabay.com/photo/2013/10/02/23/03/mountains-190055__340.jpg",
        // },
        // {
        //     id: 4,
        //     src: "https://media.istockphoto.com/id/1388623445/photo/bear-skin-state-trail-bridge.jpg?b=1&s=170667a&w=0&k=20&c=guB8b7svJuFkYd0L9SecXafAHn5eI2dZSBolrZlaA4s=",
        // },
        // {
        //     id: 5,
        //     src: "https://cdn.pixabay.com/photo/2013/10/02/23/03/mountains-190055__340.jpg",
        // },
    ];

    total_time = 3 * data.length;
    total_time = total_time+3; 
    let multiple_bar = document.getElementById("multiple_bar");
    for (let i = 1; i <= data.length+1; i++) {
        let html = ""
        html =`
        <div class="single_bar"></div>
        `;
        multiple_bar.innerHTML += html;
    }

    data.forEach(function (e, index) {
        setTimeout(function () {
            
            document.getElementById("myImg").src = e.src;
        }, 3000 * (index + 1));
    });

    
    document.getElementById("myDIV").style.animationDuration = `${total_time}s`;
    // bar.style.animationDuration="3s" ;

    setTimeout(() => {
        history.back();
    }, total_time*1000);

    function close_click() {
        history.back();
    }
</script>
