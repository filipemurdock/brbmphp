/**
 * cloutsy player
 * version: 0.2.1
 */

const playerMusics = [
    {
        name: 'Offset - Clout ft. Cardi B',
        src: 'https://cdn.smmspot.net/cloutsy/assets/music/song1.mp3',
        cover: 'https://cdn.smmspot.net/cloutsy/assets/music/clout.jpg',
    },
    {
        name: 'Louis The Child - Big Time',
        src: 'https://cdn.smmspot.net/cloutsy/assets/music/song2.mp3',
        cover: 'https://cdn.smmspot.net/cloutsy/assets/music/song2.jpg',
    },
    {
        name: 'Sea Song - Fakear',
        src: 'https://cdn.smmspot.net/cloutsy/assets/music/song3.mp3',
        cover: 'https://cdn.smmspot.net/cloutsy/assets/music/song3.jpeg',
    },
	{
        name: 'A$AP Rocky - Praise The Lord (Da Shine)',
        src: 'https://cdn.smmspot.net/cloutsy/assets/music/song4.mp3',
        cover: 'https://cdn.smmspot.net/cloutsy/assets/music/song4.jpeg',
    },
	{
        name: 'JVKE - this is what falling in love feels like',
        src: 'https://cdn.smmspot.net/cloutsy/assets/music/song5.mp3',
        cover: 'https://cdn.smmspot.net/cloutsy/assets/music/song5.jpeg',
    },
	{
        name: 'J.Cole - She Knows',
        src: 'https://cdn.smmspot.net/cloutsy/assets/music/song6.mp3',
        cover: 'https://cdn.smmspot.net/cloutsy/assets/music/song6.jpeg',
    },
	{
        name: 'Jnr Choi - TO THE MOON',
        src: 'https://cdn.smmspot.net/cloutsy/assets/music/song7.mp3',
        cover: 'https://cdn.smmspot.net/cloutsy/assets/music/song7.jpeg',
    },
	{
        name: 'WILLOW, Tyler Cole - Meet Me At Our Spot',
        src: 'https://cdn.smmspot.net/cloutsy/assets/music/song8.mp3',
        cover: 'https://cdn.smmspot.net/cloutsy/assets/music/song8.jpeg',
    },
	{
        name: 'NEIKED, Mae Muller, J Balvin - Better Days ft. Polo G',
        src: 'https://cdn.smmspot.net/cloutsy/assets/music/song9.mp3',
        cover: 'https://cdn.smmspot.net/cloutsy/assets/music/song9.jpeg',
    },
	{
        name: 'Quinn XCII - Straightjacket',
        src: 'https://cdn.smmspot.net/cloutsy/assets/music/song10.mp3',
        cover: 'https://cdn.smmspot.net/cloutsy/assets/music/song10.jpeg',
    },
	{
        name: 'Tai Verdes - LAst dAy oN EaRTh',
        src: 'https://cdn.smmspot.net/cloutsy/assets/music/song11.mp3',
        cover: 'https://cdn.smmspot.net/cloutsy/assets/music/song11.jpeg',
    },
	{
        name: 'Charlie Puth - Light Switch',
        src: 'https://cdn.smmspot.net/cloutsy/assets/music/song12.mp3',
        cover: 'https://cdn.smmspot.net/cloutsy/assets/music/song12.jpeg',
    },
	{
        name: 'AURORA - Runaway',
        src: 'https://cdn.smmspot.net/cloutsy/assets/music/song13.mp3',
        cover: 'https://cdn.smmspot.net/cloutsy/assets/music/song13.jpeg',
    },
	{
        name: 'Ed Sheeran - Shape of You',
        src: 'https://cdn.smmspot.net/cloutsy/assets/music/song14.mp3',
        cover: 'https://cdn.smmspot.net/cloutsy/assets/music/song14.jpeg',
    },
	{
        name: 'G-Eazy - I Mean It Ft. Remo',
        src: 'https://cdn.smmspot.net/cloutsy/assets/music/song15.mp3',
        cover: 'https://cdn.smmspot.net/cloutsy/assets/music/song15.jpeg',
    },
	{
        name: 'The Beat Drops - Carlton E Bynum II',
        src: 'https://cdn.smmspot.net/cloutsy/assets/music/song16.mp3',
        cover: 'https://cdn.smmspot.net/cloutsy/assets/music/song16.jpeg',
    },
	{
        name: 'GAYLE - â€‹ABCDEFU',
        src: 'https://cdn.smmspot.net/cloutsy/assets/music/song17.mp3',
        cover: 'https://cdn.smmspot.net/cloutsy/assets/music/song17.jpeg',
    },
	{
        name: 'Doja Cat, The Weeknd - You Right',
        src: 'https://cdn.smmspot.net/cloutsy/assets/music/song18.mp3',
        cover: 'https://cdn.smmspot.net/cloutsy/assets/music/song18.jpeg',
    },
	{
        name: 'Phantogram - Fall In Love',
        src: 'https://cdn.smmspot.net/cloutsy/assets/music/song19.mp3',
        cover: 'https://cdn.smmspot.net/cloutsy/assets/music/song19.jpeg',
    },
	{
        name: 'Kiss Me More - Doja Cat ft. SZA',
        src: 'https://cdn.smmspot.net/cloutsy/assets/music/song20.mp3',
        cover: 'https://cdn.smmspot.net/cloutsy/assets/music/song20.jpeg',
    },
	{
        name: 'Mood (Remix) - 24kGoldn, Justin Bieber, J Balvin, iann dior',
        src: 'https://cdn.smmspot.net/cloutsy/assets/music/song21.mp3',
        cover: 'https://cdn.smmspot.net/cloutsy/assets/music/song21.jpeg',
    },
	{
        name: 'Savage Love - Jason Derulo',
        src: 'https://cdn.smmspot.net/cloutsy/assets/music/song22.mp3',
        cover: 'https://cdn.smmspot.net/cloutsy/assets/music/song22.jpeg',
    },
	{
        name: 'SOFIA - ALVARO SOLER',
        src: 'https://cdn.smmspot.net/cloutsy/assets/music/song23.mp3',
        cover: 'https://cdn.smmspot.net/cloutsy/assets/music/song23.jpeg',
    },
	{
        name: 'Feels - Calvin Harris, Pharrell Williams, Katy Perry and Big Sean',
        src: 'https://cdn.smmspot.net/cloutsy/assets/music/song24.mp3',
        cover: 'https://cdn.smmspot.net/cloutsy/assets/music/song24.jpeg',
    },
	{
        name: 'Me Too - Meghan Trainor',
        src: 'https://cdn.smmspot.net/cloutsy/assets/music/song25.mp3',
        cover: 'https://cdn.smmspot.net/cloutsy/assets/music/song25.jpeg',
    },
	{
        name: 'That Girl - Olly Murs',
        src: 'https://cdn.smmspot.net/cloutsy/assets/music/song26.mp3',
        cover: 'https://cdn.smmspot.net/cloutsy/assets/music/song26.jpeg',
    },
	{
        name: 'Good Mornin - Meghan Trainor, Gary Trainor',
        src: 'https://cdn.smmspot.net/cloutsy/assets/music/song27.mp3',
        cover: 'https://cdn.smmspot.net/cloutsy/assets/music/song27.jpeg',
    },
	{
        name: 'Vacation - Tyga',
        src: 'https://cdn.smmspot.net/cloutsy/assets/music/song28.mp3',
        cover: 'https://cdn.smmspot.net/cloutsy/assets/music/song28.jpeg',
    },
	{
        name: 'Carry On - Kygo, Rita Ora',
        src: 'https://cdn.smmspot.net/cloutsy/assets/music/song29.mp3',
        cover: 'https://cdn.smmspot.net/cloutsy/assets/music/song29.jpeg',
    },
	{
        name: 'My Oh My - Camila Cabello, DaBaby',
        src: 'https://cdn.smmspot.net/cloutsy/assets/music/song30.mp3',
        cover: 'https://cdn.smmspot.net/cloutsy/assets/music/song30.jpeg',
    },
	{
        name: 'Lights Up - Harry Styles',
        src: 'https://cdn.smmspot.net/cloutsy/assets/music/song30.mp3',
        cover: 'https://cdn.smmspot.net/cloutsy/assets/music/song30.jpeg',
    },

];

const sidebarPlayer = document.querySelector('.sidebar-player');
const [activeSong, setActiveSong] = useState(0);
const [autoPlay, setAutoPlay] = useState('disabled');
const [firstPlay, setFirstPlay] = useState('true');

if (sidebarPlayer) {
    const albumCover = sidebarPlayer.querySelector('.album-cover > img');
    const songName = sidebarPlayer.querySelector('.song-name');
    const autoPlayBtn = sidebarPlayer.querySelector('#sound-autoplay');
    const totalSongDom = sidebarPlayer.querySelector('#total-song');
    const currentSongDom = sidebarPlayer.querySelector('#current-song-order');

    totalSongDom.innerHTML = playerMusics.length;

    if (localStorage.getItem('activeSong')) {
        setActiveSong(parseInt(localStorage.getItem('activeSong')));
    }

    if (localStorage.getItem('autoPlay')) {
        setAutoPlay(localStorage.getItem('autoPlay'));
    } else {
        localStorage.setItem('autoPlay', 'enabled');
    }

    const songPosition = (localStorage.getItem('songPosition')) ? parseInt(localStorage.getItem('songPosition')) : 0;

    const setActive = () => {
        albumCover.src = `${playerMusics[activeSong()].cover}`;
        songName.innerHTML = playerMusics[activeSong()].name;
        currentSongDom.innerHTML = activeSong() + 1;
    }

    // prepare the player
    let pi = 0;
    var sound = [];
    playerMusics.forEach(music => {
        const soundItem = new Howl({
            src: [playerMusics[pi].src],
            html5: true,
            preload: false,
            onplay: function () {
                sidebarPlayer.classList.add('playing');
                if (firstPlay() == 'true') {
                    soundItem.seek(songPosition);
                    setFirstPlay('false');
                }
                // watch position
                var positionFunc = () => {
                    var duration = soundItem.duration();
                    var position = soundItem.seek();
                    localStorage.setItem('songPosition', position);
                    this.savePosition = setTimeout(() => positionFunc(), 1000);
                }
                positionFunc();
            },
            onstop: function () {
                clearTimeout(this.savePosition);
            },
            onpause: function () {
                sidebarPlayer.classList.remove('playing');
            },
            onend: function () {
                nextSong();
            }
        });
        sound.push(soundItem);
        pi++;
    })

    if (autoPlay() === 'enabled') {
        autoPlayBtn.classList.add('enabled');
        sound[activeSong()].play();
        setActive();
    } else {
        setActive();
    }

    // next song
    const nextSong = () => {
        sound[activeSong()].stop();
        if (activeSong() < playerMusics.length - 1) {
            setActiveSong(parseInt(activeSong()) + 1);
        } else {
            setActiveSong(0);
        }
        sound[activeSong()].play();
        localStorage.setItem('activeSong', activeSong());

        setActive();
    }

    // previous song
    const prevSong = () => {
        sound[activeSong()].stop();
        if (activeSong() > 0) {
            setActiveSong(parseInt(activeSong()) - 1);
        } else {
            setActiveSong(playerMusics.length - 1);
        }
        sound[activeSong()].play();
        localStorage.setItem('activeSong', activeSong());
        setActive();
    }

    sidebarPlayer.querySelector('#song-next').addEventListener('click', nextSong);
    sidebarPlayer.querySelector('#song-prev').addEventListener('click', prevSong);

    // play and pause
    sidebarPlayer.querySelector('#play-pause').addEventListener('click', () => {
        if (sound[activeSong()].playing()) {
            sound[activeSong()].pause();
            sidebarPlayer.classList.remove('playing');
        } else {
            sound[activeSong()].play();
            sidebarPlayer.classList.add('playing');
        }
    });

    // auto play button
    autoPlayBtn.addEventListener('click', () => {
        if (autoPlay() === 'enabled') {
            autoPlayBtn.classList.remove('enabled');
            localStorage.setItem('autoPlay', 'disabled');
            setAutoPlay('disabled');
        } else {
            autoPlayBtn.classList.add('enabled');
            localStorage.setItem('autoPlay', 'enabled');
            setAutoPlay('enabled');
        }
    });
}