const url = JSON.parse(document.getElementById('data').innerHTML).url;

class User {
    constructor(id, name, img) {
      this.id = id;
      this.name = name;
      this.img = img;
    }
}

class Room {
    constructor(id, name, dayStart, dayEnd, hrStart, hrEnd){
        this.id = id;
        this.name = name;
        this.dayStart = dayStart;
        this.dayEnd = dayEnd;
        this.hrStart = hrStart;
        this.hrEnd = hrEnd;
    }
}

class Booking {
    constructor(id, user, datetimeStart, datetimeEnd, room){
        this.id = id;
        this.user = user;
        this.datetimeStart = datetimeStart;
        this.datetimeEnd = datetimeEnd;
        this.room = room;
    }
}

const Users = { 
    data: function () {
        return {
            items: []
        }
      },
      methods: {
          getItems: function() {
              return this.items;
          }
      },
      computed: {
        filteredList() {
          return this.items.filter(post => {
            return post.name.toLowerCase().includes(app.$data.search.toLowerCase())
          })
        }
      },
      template:
       '<div>'+
            '<div v-for="post in filteredList"><img :src="post.img" class="image--cover">{{post.name}} {{post.id}}</div>'+
        '</div>',
    mounted(){
        axios
            .get(url + "/send.php?req=getAllUsers")
            .then((result) => {
                for(let i = 0; i < result.data.members.length; i++){
                    this.items.push(new User(result.data.members[i].id, result.data.members[i].profile.real_name,result.data.members[i].profile.image_48))
                }           
            })
    }
}

const Rooms = { 
    data: function () {
        return {
          items: []
        }
      },
      template:
       '<div>'+
            '<div v-for="post in filteredList">{{post.name}}</div>'+
        '</div>',
        computed: {
            filteredList() {
              return this.items.filter(post => {
                return post.name.toLowerCase().includes(app.$data.search.toLowerCase())
              })
            }
          },
    mounted(){          
          axios.get(url + '/send.php?req=getAllRooms')
          .then(result => {
            for(let i = 0; i < result.data.response.length; i++){
                this.items.push(new Room(result.data.response[i].room_id, result.data.response[i].room_name, result.data.response[i].room_day_start, result.data.response[i].room_day_end, result.data.response[i].room_hr_start, result.data.response[i].room_hr_end))
            }      
          })
    }
}

const Bookings = { 
    data: function () {
        return {
          items: []
        }
      },
      template:
       '<div>'+
            '<div v-for="post in filteredList">{{post.room.name}} - réservé le {{ post.datetimeStart }} par : {{ post.user.name }} </div>'+
        '</div>',
        computed: {
            filteredList() {
              return this.items.filter(post => {
                return post.room.name.toLowerCase().includes(app.$data.search.toLowerCase()) || post.user.name.toLowerCase().includes(app.$data.search.toLowerCase())
              })
            }
          },
    mounted(){          
        axios.all([
            axios.get(url + '/send.php?req=getAllUsers'),
            axios.get(url + '/send.php?req=getAllRooms'),
            axios.get(url + '/send.php?req=getAllBookings')
          ])
          .then(axios.spread((usersSlack, rooms, bookings) => {
            for(let i = 0; i < bookings.data.response.length; i++){
                let searchUserSlackWithId = function(){
                    for(let j=0; j<usersSlack.data.members.length; j++) {
                          if(usersSlack.data.members[j]["id"] == bookings.data.response[i].booking_user ) {
                           return new User(usersSlack.data.members[j].id, 
                                            usersSlack.data.members[j].profile.real_name,
                                            usersSlack.data.members[j].profile.image_48);
                          }
                    }
                }

                let searchRoomWithId = function(){
                    for(let j=0; j<rooms.data.response.length; j++) {
                          if(rooms.data.response[j]["room_id"] == bookings.data.response[i].booking_room ) {
                            return new Room(rooms.data.response[j].room_id, 
                                            rooms.data.response[j].room_name,
                                            rooms.data.response[j].room_day_start, 
                                            rooms.data.response[j].room_day_end,
                                            rooms.data.response[j].room_hr_start, 
                                            rooms.data.response[j].room_hr_end)
                          }
                    }

                }
                this.items.push(new Booking(bookings.data.response[i].booking_id,
                                            searchUserSlackWithId(),
                                            bookings.data.response[i].booking_datetime_start,
                                            bookings.data.response[i].booking_datetime_end,
                                            searchRoomWithId()
                ))

        }

          }))
        }
}

const Home = {
  template: "<div>Accueil</div>"
} 

const routes = [
    { path: '/users', component: Users },
    { path: '/rooms', component: Rooms },
    { path: '/booking', component: Bookings },
    { path: '/', component: Home },
     // { path: '*', component: NotFound }
  ]

const router = new VueRouter({
    mode: 'history',
    routes 
})

const app = new Vue({
    data: {
        search: '',
    },
    router,

}).$mount('#app')

