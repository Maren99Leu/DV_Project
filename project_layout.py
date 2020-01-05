import dash
import dash_core_components as dcc
import dash_html_components as html
from dash.dependencies import Input, Output

import numpy as np
import pandas as pd
import plotly.graph_objs as go
import plotly.offline as pyo
from plotly.subplots import make_subplots

import numpy as np

pd.set_option('display.max_columns', 20)


df = pd.read_csv('emission_full.csv')

#df = df.fillna(0)

df = df.rename(columns = {'GHG_emissions': 'GHG Emissions', 'CH4_emissions': 'CH4 Emissions', 'N2O_emissions': 'N2O Emissions',
                'F_Gas_emissions': 'F Gas Emissions', 'CO2_emissions': 'CO2 Emissions'})

places= ['energy_emissions', 'industry_emissions',
       'agriculture_emissions', 'waste_emissions',
       'land_use_foresty_emissions', 'bunker_fuels_emissions',
       'electricity_heat_emissions', 'construction_emissions',
       'transports_emissions', 'other_fuels_emissions']

df['continent'] = df['continent'].fillna('World')


#Emissions negativas no CO2,
#print(df[df['CO2 Emissions'] < 0].head())

df['CO2 Emissions'] = df['CO2 Emissions'].abs()
#print(df[df['CO2 Emissions'] < 0].head())


country_options = [dict(label=country, value=country) for country in df['country_name'].unique()]


year = [dict(label = year, value = year) for year in df['year'].unique()]

gases_list = ['GHG Emissions', 'CH4 Emissions', 'N2O Emissions', 'F Gas Emissions', 'CO2 Emissions']


gases = [dict(label = gas.replace('_', ' '), value = gas) for gas in gases_list]


external_stylesheets = ['https://codepen.io/chriddyp/pen/bWLwgP.css']

app = dash.Dash(__name__, external_stylesheets=external_stylesheets)

app.layout = html.Div([

    html.Div([
        html.H1('World Emissions - Data Visualization Project'),
        html.H3('by Catarina, Mafalda and Maren')
    ], className='Title'),

    html.Div([

        html.Div([
            html.Label('Year Slider'),
            dcc.Slider(
                id='year_slider',
                min=df['year'].min(),
                max=df['year'].max(),
                #marks={str(year): str(year) for year in df['year'].unique()},
                marks={str(i): '{}'.format(str(i)) for i in [1990, 1995, 2000, 2005, 2010, 2014]},
                value=df['year'].min(),
                step=1
            ),

            html.Br(),

            html.Label('Choose a Projection'),
            dcc.RadioItems(
                    id='projection',
                    options=[dict(label='Equirectangular', value=0), dict(label='Orthographic', value=1)],
                    value=0,
                    labelStyle={'display': 'inline-block'}
            ),

            html.Br(),

            html.Label('Select a Gas'),
            dcc.RadioItems(
                id='checkbox_gases',
                options=gases,
                value='GHG Emissions',
                labelStyle={'display': 'inline-block'}
            ),

            html.Br(),

            html.Label('Choose a country'),
            dcc.Dropdown(
                id='country_drop',
                options=country_options,
                value='Portugal',
                multi=False
            ),
        ], className='column1 pretty'),

        html.Div([

            html.Div([

                html.Div([html.Label(id='gas_1')], className='mini pretty'),
                html.Div([html.Label(id='gas_2')], className='mini pretty'),
                html.Div([html.Label(id='gas_3')], className='mini pretty'),
                html.Div([html.Label(id='gas_4')], className='mini pretty'),
                html.Div([html.Label(id='gas_5')], className='mini pretty'),

            ], className='5 containers row'),

#################### MAP #######################
            html.Div([dcc.Graph(id = 'map')], className='column3 pretty'),
        ], className='column2')

    ], className='row'),
#################### 2 BAR PLOTS ##################
    html.Div([
        html.Div([dcc.Graph(id = 'bar_plot')], className='column3 pretty'),

        html.Div([dcc.Graph(id = 'bar_plot_cont')], className='column3 pretty')
    ], className='row'),

    html.Div([
        html.Div([
            html.Label('Time Series Graph'),
            dcc.Graph(id = 'time_series')
        ], className='column3 pretty')
    ], className='row')

])

@app.callback([
                   Output("bar_plot", "figure"),
                   Output('map', 'figure'),
                   Output("bar_plot_cont", "figure"),
                   #Output("fig_time", "figure")

               ],
              [
                  Input("country_drop", "value"),
                  Input("year_slider", "value"),
                  #Input("lin_log", "value"),
                  Input("projection", "value"),
                  Input('checkbox_gases', 'value')
               ])

def UpdateGraph(country, year, projection, gas):

    df_ = df[(df['country_name'] == country) & (df['year'] == year)]


    fig = go.Figure()

    fig.add_trace(go.Bar(
        x = [df_.iloc[0]['year']],
        y = [df_.iloc[0]['CH4 Emissions']],
        name = 'CH4 Emissions'))

    fig.add_trace(go.Bar(
        x = [df_.iloc[0]['year']],
        y = [df_.iloc[0]['N2O Emissions']],
        name = 'N20 Emissions'))

    fig.add_trace(go.Bar(
        x = [df_.iloc[0]['year']],
        y = [df_.iloc[0]['F Gas Emissions']],
        name = 'F Gas Emissions'))

    fig.add_trace(go.Bar(
        x = [df_.iloc[0]['year']],
        y = [df_.iloc[0]['CO2 Emissions']],
        name = 'CO2 Emissions'))


    ### MAP ###

    df_map = df.loc[df['year'] == year]

    z = np.log(df_map[gas])

    #print(z)
    #print(df_map['country_name'])

    data_choropleth = dict(type = 'choropleth',
                           locations = df_map['country_name'],
                           locationmode='country names',
                           z = z,
                           text = df_map['country_name'],
                           colorscale = 'RdYlGn',
                           reversescale = True,
                           name = '')

    layout_choropleth = dict(geo = dict(scope = 'world',  # default
                                        projection = dict(type = ['equirectangular', 'orthographic'][projection]),
                                        landcolor = 'black',
                                        lakecolor = 'white',
                                        showocean = True,
                                        oceancolor = 'azure',
                                        bgcolor = '#f9f9f9'),

                             paper_bgcolor = '#f9f9f9',
                             margin = dict(t = 0, b = 0, l = 0, r = 0)
                             )

    map = go.Figure(data = data_choropleth, layout = layout_choropleth)


    ## CONTINENT BAR PLOT ##


    df_grouped = df.groupby(['continent', 'year'])['GHG Emissions', 'CH4 Emissions', 'N2O Emissions', 'F Gas Emissions', 'CO2 Emissions'].sum().reset_index()

    df_grouped = df_grouped[df_grouped['year'] == year]

    df__ = df[(df['year'] == year) & (df['country_name'] == country)].reset_index()

    continents = ['Africa', 'Asia', 'Europe', 'North America', 'Oceania', 'Seven seas (open ocean)', 'South America',
                  'World']

    bar_cont = go.Figure(data = [
        go.Bar(name = str(gas), x = continents, y = df_grouped[gas]),
        go.Bar(name = df__['country_name'][0] + ' ' + str(gas), x = continents, y = float(df__[gas].values)*np.ones(8))])

    bar_cont.update_layout(barmode = 'group')

    ###

    return fig, map, bar_cont

@app.callback(
    [
        Output("gas_1", "children"),
        Output("gas_2", "children"),
        Output("gas_3", "children"),
        Output("gas_4", "children"),
        Output("gas_5", "children")
    ],
    [
        Input("country_drop", "value"),
        Input("year_slider", "value"),
    ]
)

def indicator(countries, year):
    #df_gas = df.loc[df['country_name'].isin(countries)].groupby('year').sum().reset_index()
    df_loc = df.loc[df['country_name'].isin(countries)].groupby('year').sum().reset_index()
    value_1 = round(df_loc.loc[df_loc['year'] == year][gases_list[0]].values[0], 2)
    value_2 = round(df_loc.loc[df_loc['year'] == year][gases_list[1]].values[0], 2)
    value_3 = round(df_loc.loc[df_loc['year'] == year][gases_list[2]].values[0], 2)
    value_4 = round(df_loc.loc[df_loc['year'] == year][gases_list[3]].values[0], 2)
    value_5 = round(df_loc.loc[df_loc['year'] == year][gases_list[4]].values[0], 2)

    return str(gases_list[0]).replace('_', ' ') + ': ' + str(value_1), \
           str(gases_list[1]).replace('_', ' ') + ': ' + str(value_2), \
           str(gases_list[2]).replace('_', ' ') + ': ' + str(value_3), \
           str(gases_list[3]).replace('_', ' ') + ': ' + str(value_4), \
           str(gases_list[4]).replace('_', ' ') + ': ' + str(value_5),


if __name__ == '__main__':
    app.run_server(debug = True)
    

"""
html.Label('Linear Log'),
    dcc.RadioItems(
        id='lin_log',
        options=[dict(label='Linear', value=0), dict(label='log', value=1)],
        value=0
    ),

"""

"""
dcc.Slider(
id='year_slider',
min=df['year'].min(),
max=df['year'].max(),
value= df['year'].min(),
marks={str(year): str(year) for year in df['year'].unique()},
step=None),
"""

"""
return (dcc.Graph(
            id='bar_plot',
            figure = {
            'data': [
            # {'x': df['year'], 'y': df['GHG_emissions'], 'type': 'bar', 'name': 'GHG Emissions'},
            {'x': df_['year'], 'y': df_['CH4_emissions'], 'type': 'bar', 'name': 'CH4 Emissions'},
            {'x': df_['year'], 'y': df_['N2O_emissions'], 'type': 'bar', 'name': 'N2O_emissions'},
            {'x': df_['year'], 'y': df_['F_Gas_emissions'], 'type': 'bar', 'name': 'F_Gas_emissions'},
            {'x': df_['year'], 'y': df_['CO2_emissions'], 'type': 'bar', 'name': 'CO2_emissions'}
            ],
            'layout': {
            'title': 'Emissions by Country and year'
            }
            }
            ))
"""

