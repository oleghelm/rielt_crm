var TicketsSection = React.createClass({
    getInitialState: function() {
        return {
            tickets: [],
            free_tickets: []
        }
    },

    componentDidMount: function() {
        this.loadTicketsFromServer();
//        setInterval(this.loadTicketsFromServer, 20000);
    },

    loadTicketsFromServer: function() {
        $.ajax({
            url: this.props.url,
            success: function (data) {
                this.setState({tickets: data.tickets.today,free_tickets:data.tickets.free});
            }.bind(this)
        });
    },

    render: function() {
        return (
            <div className="row">
                <div className="notes-container">
                    <h2 className="notes-header">Задачі</h2>
                    <div><a href="/crm/tickets/new" className="ajax-page-load"><i className="fa fa-plus plus-btn"></i></a></div>
                    <div><a className="" onClick={this.loadTicketsFromServer}><i className="fa fa-minus plus-btn"></i></a></div>
                </div>
                <div className="col-md-6 col-sm-12">
                    <TicketsList tickets={this.state.tickets} title="Завдання на сьогодні" />
                </div>
                <div className="col-md-6 col-sm-12">
                    <TicketsList tickets={this.state.free_tickets} title="Доступні завдання" />
                </div>
            </div>
        );
    }
});

var TicketsList = React.createClass({
    render: function() {
        var ticketNodes = '';
        if(this.props.tickets)
            ticketNodes = this.props.tickets.map(function(ticket) {
                console.log(ticket);
                return (
                    <TicketBox ticket={ticket} key={ticket.id}>{ticket.id}</TicketBox>
                );
            });

        return (
            <div className="container-fluid">
                <hr />
                <h3>{this.props.title}</h3>
                <div className="row ticket-items">
                    {ticketNodes}
                </div>
            </div>
        );
    }
});

var TicketBox = React.createClass({
    RenderInforow: function(name,value){
        if(value){
            return (<div className="inforow">
                        <div className="title">{name}</div>
                        <div className="val">{value}</div>
                    </div>);
        }
    },
    render: function() {
        return (
            <div className="col-md-12 col-sm-12 com-xs-12">
                <div className={"ticket-item status-"+this.props.ticket.status_code}>
                    {this.RenderInforow('Виконавець',this.props.ticket.username)}
                    {this.RenderInforow('Дата',this.props.ticket.date)}
                    {this.RenderInforow('Час',this.props.ticket.time)}
                    {this.RenderInforow('Тип',this.props.ticket.task)}
                    {this.RenderInforow('Статус',this.props.ticket.status)}
                    <div className="clearfix"></div>
                    <div className="inforow buttons">
                        <a href={this.props.ticket.links.setstatus} className="btn btn-primary btn-sm ajax-page-load" ><i className="fa fa-unsorted"></i></a>&nbsp;
                        <a href={this.props.ticket.links.show} className="btn btn-success btn-sm ajax-page-load" ><i className="fa fa-arrows"></i></a>&nbsp;
                        {this.props.ticket.username ?
                            ''
                            : <a href={this.props.ticket.links.grabit} className="btn btn-primary btn-sm ajax-grab-ticket" ><i className="fa fa-hand-grab-o"></i></a>
                        }
                    </div>
                    <div className="clearfix"></div>
                </div>
            </div>
        );
    }
});

window.TicketsSection = TicketsSection;
