import dash
import dash_core_components as dcc
import dash_html_components as html
from dash.dependencies import Input, Output
import numpy as np
import pandas as pd
import plotly.graph_objs as go

######################################################Data##############################################################

df = pd.read_csv('data.csv', sep = ';')

gas_names = ['CO2_emissions', 'CH4_emissions','N2O_emissions', 'GHG_emissions', 'GDP']

places= ['energy_emissions', 'industry_emissions',
       'agriculture_emissions', 'waste_emissions',
       'land_use_foresty_emissions', 'bunker_fuels_emissions',
       'electricity_heat_emissions', 'construction_emissions',
       'transports_emissions', 'other_fuels_emissions']

######################################################Interactive Components############################################

country_options = [dict(label=country, value=country) for country in df['Country Name'].unique()]

gas_options = [dict(label=gas.replace('_', ' '), value=gas) for gas in gas_names]

##################################################APP###############################################################
external_stylesheets = ['https://codepen.io/chriddyp/pen/bWLwgP.css']

app = dash.Dash(__name__, external_stylesheets=external_stylesheets)

app.layout = html.Div([
    html.Div([
                html.H1('World Emissions - Data Visualization Project'),
                html.H4('by Catarina, Mafalda and Maren')
            ], className='Title'),

    html.Div([
        html.Div([
            html.Label('Country Choice'),
            dcc.Dropdown(
                id='country_drop',
                options=country_options,
                value=['Portugal'],
                multi=True
            ),
            html.Br(),

            html.Label('Year Slider'),
                            dcc.Slider(
                                id='year_slider',
                                min=df['year'].min(),
                                max=df['year'].max(),
                                marks={str(i): '{}'.format(str(i)) for i in [1990, 1995, 2000, 2005, 2010, 2014]},
                                value=df['year'].min(),
                                step=1
                        ),

            html.Br(),

            html.Label('Linear Log'),
            dcc.RadioItems(
                id='lin_log',
                options=[dict(label='Linear', value=0), dict(label='log', value=1)],
                value=0
            ),

            html.Br(),

            html.Label('Projection'),
            dcc.RadioItems(
                id='projection',
                options=[dict(label='Equirectangular', value=0), dict(label='Orthographic', value=1)],
                value=0
            ),

            html.Br(),

            html.Label('Gas Choice'),
                dcc.Dropdown(
                    id='gas_option',
                    options=gas_options,
                    value='CO2_emissions',
                ),

            html.Br(),

        ], className='column1 pretty'),

        html.Div([
            html.Div([dcc.Tabs([
                        dcc.Tab(label='World Map', children=[
                            dcc.Graph(id='choropleth')
                        ]),
                        dcc.Tab(label='Time Series Data', children=[
                            dcc.Graph(id='bar_graph')
                        ]),
                        dcc.Tab(label='Continental Bar Plot', children=[
                            dcc.Graph(id='bar_cont')
                        ]),
                    ])
                ]),
            ], className='column2 pretty')
    ], className='row')
])


######################################################Callbacks#########################################################
@app.callback(
    [
        Output("bar_graph", "figure"),
        Output("choropleth", "figure"),
        #Output("bar_cont", "figure")

    ],
    [
        Input("year_slider", "value"),
        Input("country_drop", "value"),
        Input("gas_option", "value"),
        Input("lin_log", "value"),
        Input("projection", "value"),
    ]
)

def plots(year, countries, gas, scale, projection):

    ############################################First Bar Plot##########################################################
    ############## Time-Series Plot ##################
    # Create figure
    ############################################First Bar Plot##########################################################
    data_bar = []
    for country in countries:
        df_bar = df.loc[(df['Country Name'] == country)]

        x_bar = df_bar['year']
        y_bar = df_bar[gas]

        data_bar.append(dict(type='scatter', x=x_bar, y=y_bar, name=country))

    layout_bar = dict(title=dict(text='Emissions from 1990 until 2015'),
                      xaxis=go.layout.XAxis(
                          rangeselector=dict(
                              buttons=list([
                                  dict(count=1,
                                       label="YTD",
                                       step="year",
                                       stepmode="todate"),
                                  dict(count=5,
                                       label="5y",
                                       step="year",
                                       stepmode="backward"),
                                  dict(count=10,
                                       label="10y",
                                       step="year",
                                       stepmode="backward"),
                                  dict(step="all")
                              ])
                          ),
                          rangeslider=dict(
                              visible=True

                          ),
                          type="date"
                      ),
                      yaxis=dict(title='Emissions', type=['linear', 'log'][scale]),
                      paper_bgcolor='#f9f9f9'
                      )
    #############################################Second Choropleth######################################################

    df_map = df.loc[df['year'] == year]

    z = np.log(df_map[gas])

    # print(z)
    # print(df_map['Country Name'])

    data_choropleth = dict(type='choropleth',
                           locations=df_map['Country Name'],
                           locationmode='country names',
                           z=z,
                           text=df_map['Country Name'],
                           colorscale='RdYlGn',
                           reversescale=True,
                           name='')

    layout_choropleth = dict(geo=dict(scope='world',  # default
                                      projection=dict(type=['equirectangular', 'orthographic'][projection]),
                                      landcolor='black',
                                      lakecolor='white',
                                      showocean=True,
                                      oceancolor='azure',
                                      bgcolor='#f9f9f9'),

                             paper_bgcolor='#f9f9f9',
                             margin=dict(t=0, b=0, l=0, r=0)
                             )

    map = go.Figure(data=data_choropleth, layout=layout_choropleth)

    return go.Figure(data=data_bar, layout=layout_bar),\
           map,\
           #bar_cont
    #go.Figure(data=data_time, layout=layout_time), \

            #go.Figure(data=data_choropleth, layout=layout_choropleth),\
            #


if __name__ == '__main__':
    app.run_server(debug=True)