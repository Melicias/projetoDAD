const template = `
    <form action="#" class="form-inline">
        <div class="form-group">
            <label for="task">Task</label>
            <input type="text" class="form-control" id="task" placeholder="Enter the task description"
                size="50" v-model="newTask">
        </div>
        <button type="submit" class="btn btn-info" @click.prevent="addTask">
            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
        </button>
    </form>
    <br />
    <ul id="tasks"  class="list-group col-md-5">
        <li v-for="(task,idx) in tasks" class="list-group-item">
            <span :class="{'completed': task.completed}">{{task.description}}</span>
            <div class="pull-right">
                <button v-if="!task.completed" class="btn btn-success btn-xs" @click="task.completed = !task.completed">
                    <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                </button>
                <button v-else class="btn btn-primary btn-xs" @click="task.completed = !task.completed">
                    <span class="glyphicon glyphicon-repeat" aria-hidden="true"></span>
                </button>
                <button class="btn btn-danger btn-xs" v-on:click="deteleTask(idx)">
                    <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                </button>
            </div>
        </li>
    </ul>
`;

const Todo = {
    template: template,
    data() {
        return {
            tasks: [],
            newTask: "",
        };
    },
    methods: {
        addTask() {
            if (this.newTask.trim() == "") return;
            let newT = { completed: false, description: this.newTask };
            this.tasks.push(newT);
            this.newTask = "";
        },
        deteleTask: function (idx) {
            this.tasks.splice(idx, 1);
        },
    },
};

Vue.createApp(Todo).mount("#todoapp");
