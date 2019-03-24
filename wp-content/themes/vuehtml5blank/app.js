const url = JSON.parse(document.getElementById("data").innerHTML).url;


Vue.filter("momentTime", function(date) {
  return moment(date)
    .locale("fr")
    .format("HH:mm");
});

Vue.filter("momentDate", function(date) {
  return moment(date)
    .locale("fr")
    .format("L");
});
Vue.filter("momentDay", function(date) {
  let arr = {
    0: "dimanche",
    1: "lundi",
    2: "mardi",
    3: "mercredi",
    4: "jeudi",
    5: "vendredi",
    6: "samedi"
  };
  return arr[date];
});

class User {
  constructor(id, name, img) {
    this.id = id;
    this.name = name;
    this.img = img;
  }
}

class Room {
  constructor(id, name, dayStart, dayEnd, hrStart, hrEnd, building) {
    this.id = id;
    this.name = name;
    this.dayStart = dayStart;
    this.dayEnd = dayEnd;
    this.hrStart = hrStart;
    this.hrEnd = hrEnd;
    this.building = building;
  }
}

class Booking {
  constructor(id, user, datetimeStart, datetimeEnd, room) {
    this.id = id;
    this.user = user;
    this.datetimeStart = datetimeStart;
    this.datetimeEnd = datetimeEnd;
    this.room = room;
  }
}

const Users = {
  data: function() {
    return {
      items: [],
      load: true
    };
  },
  methods: {
    getItems: function() {
      return this.items;
    }
  },
  computed: {
    filteredList() {
      return this.items.filter(post => {
        return post.name.toLowerCase().includes(app.$data.search.toLowerCase());
      });
    }
  },
  template:
    '<div v-if="filteredList.length > 0">' +
    '<div class="row">' +
    '<div class="col col-icon"></div><div class="col">Utilisateurs</div><div class="col">Slack ID</div>' +
    "</div>" +
    '<div class="row row-bg" v-for="post in filteredList"><div class="col col-icon"><img :src="post.img" class="profil-picture image--cover"></div><div class="col">{{post.name}}</div><div class="col">{{post.id}}</div></div>' +
    "</div>" +
    '<div class="load" v-else-if="this.load"><img src="' +
    url +
    '/ressources/img/load.gif"/></div>' +
    '<div class="load" v-else>Aucun résultat</div>',
  mounted() {
    axios.get(url + "/send.php?req=getAllUsers").then(result => {
      for (let i = 0; i < result.data.members.length; i++) {
        this.items.push(
          new User(
            result.data.members[i].id,
            result.data.members[i].profile.real_name,
            result.data.members[i].profile.image_48
          )
        );
      }
      this.load = false;
    });
  }
};

const Rooms = {
  data: function() {
    return {
      items: [],
      load: true,
      location: [
        "IOT1","IOT2","IOT3"
      ]
    };
  },
  template:
    '<div v-if="filteredList.length > 0">' +
    '<div class="add"><div class="add-circle add-room" v-on:click="add(emptyRoom())"></div></div>' + 
    '<div class="row">' +
    '<div class="col col-icon"></div><div class="col">Nom de la salle</div><div class="col">Disponibilités</div><div class="col">Bâtiment</div><div class="col col-icon"></div><div class="col col-icon"></div>' +
    "</div>" +
    '<div class="row row-bg" v-bind:data-id="post.id" v-for="post in filteredList"><div class="col col-icon"></div><div class="col">{{post.name}}</div><div class="col"><div>{{post.dayStart | momentDay}} au {{post.dayEnd | momentDay}}</div><div>{{ post.hrStart | momentTime }} à {{ post.hrEnd | momentTime }}</div></div><div class="col">{{getBuilding(post.building-1)}}</div><div class="col col-icon col-modify" v-on:click=\'modify(post)\'></div><div class="col col-icon col-trash" v-on:click=\'suppr(post.id, "Salle")\'></div></div>' +
    "</div>" +
    '<div class="load" v-else-if="this.load"><img src="' +
    url +
    '/ressources/img/load.gif"/></div>' +
    '<div class="load" v-else><div class="add"><div class="add-circle add-room" v-on:click="add(emptyRoom())"></div></div><div>Aucun résultat !</div><div></div></div>',
  computed: {
    filteredList() {
      return this.items.filter(post => {
        return post.name.toLowerCase().includes(app.$data.search.toLowerCase());
      });
    }
  },
  methods: {
    suppr: function(id, str) {
      let vm = this;
      swal({
        title: "Confirmation de la suppression?",
        text: "Êtes vous sure de vouloir supprimer cet élément ?",
        buttons: [true, 'Confirmer'],
        dangerMode: true,
    })
    .then((del) => {
        if (del) {
            axios.get(url + '/send.php?req=delete&id=' + id + '&type=' + str)
                .then(function(response) {
                    if (response.data.error === false) {
                        for(let i = 0; i < vm.items.length; i++){
                          if(vm.items[i].id == id){
                            vm.$delete(vm.items, i);
                            break;
                          }
                        }
                        swal("Votre " + str + " a été correctement supprimé !", {
                            icon: "success",
                        });
                    } else {
                        swal("Votre " + str + " n'a pas été correctement supprimé !", {
                            icon: "error",
                        });
                    }
                })
        }
    });
    },
    modify(room) {
      modify(room)
    },
    add(room){
      let vm = this;
      swal({
        title: "Ajout d'une salle",
        content: buildForm(room)
    }).then((add) => {
        if (add) {
            axios.get(url + '/send.php', {
                params: {
                    req: "add",
                    name: document.querySelector("#inputRoomName").value,
                    dayStart: document.querySelector("#selectDayStart").value,
                    dayEnd: document.querySelector("#selectDayEnd").value,
                    hrStart: document.querySelector("#selectHrStart").value,
                    hrEnd: document.querySelector("#selectHrEnd").value,
                    building: document.querySelector("#selectBuilding").value,
                }
            }).then(function(response) {
                if (response.data.error === false) {
                  axios.get(url + "/send.php?req=getAllRooms").then(result => {
                    let newItems = [];
                    for (let i = 0; i < result.data.response.length; i++) {
                      newItems.push(
                        new Room(
                          result.data.response[i].room_id,
                          result.data.response[i].room_name,
                          result.data.response[i].room_day_start,
                          result.data.response[i].room_day_end,
                          result.data.response[i].room_hr_start,
                          result.data.response[i].room_hr_end,
                          result.data.response[i].room_building
                        )
                      );
                        }
                      vm.items = newItems;
                      });
                    swal("Votre Salle a été créé correctement !", {
                                icon: "success",
                    });
                } else {
                    swal("Votre Salle n'a pas été créé !", {
                                icon: "error",
                            });
                }
            })
        }
    });
    },
    getBuilding(id){
      return this.location[id]
    },
    emptyRoom(){
      return new Room('','',1,0,'1990-01-01 00:00','1990-01-01 23:30',1)
    }
  },
  mounted() {
    axios.get(url + "/send.php?req=getAllRooms").then(result => {
      for (let i = 0; i < result.data.response.length; i++) {
        this.items.push(
          new Room(
            result.data.response[i].room_id,
            result.data.response[i].room_name,
            result.data.response[i].room_day_start,
            result.data.response[i].room_day_end,
            result.data.response[i].room_hr_start,
            result.data.response[i].room_hr_end,
            result.data.response[i].room_building
          )
        );
      }
      this.load = false;
    });
  }
};

const Bookings = {
  data: function() {
    return {
      items: [],
      load: true
    };
  },
  template:
    '<div v-if="filteredList.length > 0">' +
    '<div class="row">' +
    '<div class="col col-icon"></div><div class="col">Nom de la salle</div><div class="col">Date réservation</div><div class="col">Responsable</div><div class="col col-icon"></div>' +
    '</div>' +
    '<div class="row row-bg" v-bind:data-id="post.id" v-for="post in filteredList"><div class="col col-icon"></div><div class="col">{{post.room.name}}</div><div class="col"><div>{{ post.datetimeStart | momentDate}}</div><div>{{ post.datetimeStart | momentTime}} à {{ post.datetimeEnd | momentTime}}</div></div><div class="col"><img :src="post.user.img" class="image--cover">{{ post.user.name }}</div><div class="col col-icon col-trash" v-on:click=\'suppr(post.id, "Réservation")\'></div></div>' +
    '</div>' +
    '<div class="load" v-else-if="this.load"><img src="' +
    url +
    '/ressources/img/load.gif"/></div>' +
    '<div class="load" v-else>Aucun résultat</div>',
  computed: {
    filteredList() {
      return this.items.filter(post => {
        return (
          post.room.name
            .toLowerCase()
            .includes(app.$data.search.toLowerCase()) ||
          post.user.name.toLowerCase().includes(app.$data.search.toLowerCase())
        );
      });
    }
  },
  methods: {
    suppr(id, str) {
        let vm = this;
        swal({
          title: "Confirmation de la suppression?",
          text: "Êtes vous sure de vouloir supprimer cet élément ?",
          buttons: [true, 'Confirmer'],
          dangerMode: true,
      })
      .then((del) => {
          if (del) {
              axios.get(url + '/send.php?req=delete&id=' + id + '&type=' + str)
                  .then(function(response) {
                      if (response.data.error === false) {
                          for(let i = 0; i < vm.items.length; i++){
                            if(vm.items[i].id == id){
                              //document.querySelector('[data-id="' + id + '"]').remove();
                              vm.$delete(vm.items, i);
                              break;
                            }
                          }
                          swal("Votre " + str + " a été correctement supprimé !", {
                              icon: "success",
                          });
                      } else {
                          swal("Votre " + str + " n'a pas été correctement supprimé !", {
                              icon: "error",
                          });
                      }
                  })
          }
      });
    }
  },
  mounted() {
    axios
      .all([
        axios.get(url + "/send.php?req=getAllUsers"),
        axios.get(url + "/send.php?req=getAllRooms"),
        axios.get(url + "/send.php?req=getAllBookings")
      ])
      .then(
        axios.spread((usersSlack, rooms, bookings) => {
          for (let i = 0; i < bookings.data.response.length; i++) {
            let searchUserSlackWithId = function() {
              for (let j = 0; j < usersSlack.data.members.length; j++) {
                if (
                  usersSlack.data.members[j]["id"] ==
                  bookings.data.response[i].booking_user
                ) {
                  return new User(
                    usersSlack.data.members[j].id,
                    usersSlack.data.members[j].profile.real_name,
                    usersSlack.data.members[j].profile.image_48
                  );
                }
              }
            };
            let searchRoomWithId = function() {
              for (let j = 0; j < rooms.data.response.length; j++) {
                if (
                  rooms.data.response[j]["room_id"] ==
                  bookings.data.response[i].booking_room
                ) {
                  return new Room(
                    rooms.data.response[j].room_id,
                    rooms.data.response[j].room_name,
                    rooms.data.response[j].room_day_start,
                    rooms.data.response[j].room_day_end,
                    rooms.data.response[j].room_hr_start,
                    rooms.data.response[j].room_hr_end,
                    rooms.data.response[j].room_building,
                  );
                }
              }
            };
            this.items.push(
              new Booking(
                bookings.data.response[i].booking_id,
                searchUserSlackWithId(),
                bookings.data.response[i].booking_datetime_start,
                bookings.data.response[i].booking_datetime_end,
                searchRoomWithId()
              )
            );
          }
          this.load = false;
        })
      );
  }
};

const Home = {
  template: "<div>Accueil</div>",
  created() {
    //console.log(this.$router.resolve(this.$route.query.redirect.replace('/', '')));
    if (
      typeof this.$route.query.redirect !== "undefined" &&
      this.$router.resolve(this.$route.query.redirect.replace("/", ""))
    ) {
      this.$router.push(this.$route.query.redirect.replace("/", ""));
    }
  }
};

const NotFound = {
  render(h) {
    return h;
  },
  mounted() {
    this.$router.push({ name: "home" });
  }
};
const routes = [
  { path: "/users", component: Users, name: "users" },
  { path: "/rooms", component: Rooms, name: "rooms" },
  { path: "/booking", component: Bookings, name: "booking" },
  { path: "/", component: Home, name: "home" },
  { path: "*", component: NotFound }
];

const router = new VueRouter({
  mode: "history",
  routes
});

const app = new Vue({
  data: {
    search: ""
  },
  router,
  watch: {
    $route: params => {
      this.search = "";
    }
  }
}).$mount("#app");
