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

df['continent'] = df['continent'].fillna('World')


#Emissions negativas no CO2,
#print(df[df['CO2 Emissions'] < 0].head())

df['CO2 Emissions'] = df['CO2 Emissions'].abs()
#print(df[df['CO2 Emissions'] < 0].head())


country_options = [dict(label=country, value=country) for country in df['country_name'].unique()]


year = [dict(label = year, value = year) for year in df['year'].unique()]

gases_list = ['GHG Emissions', 'CH4 Emissions', 'N2O Emissions', 'F Gas Emissions', 'CO2 Emissions']


gases = [dict(label = gas, value = gas) for gas in gases_list]



external_stylesheets = ['https://codepen.io/chriddyp/pen/bWLwgP.css']

app = dash.Dash(__name__, external_stylesheets=external_stylesheets)

app.layout = html.Div([
    html.H1(children='World Emissions - Data Visualization Project',
            style = {'textAlign': 'center'}
            ),

    html.Div(children='By Catarina, Mafalda and Maren',
             style = {'textAlign': 'center'}),

    html.Hr(),

    html.Br(),

    dcc.Slider(
        id='year_slider',
        min=df['year'].min(),
        max=df['year'].max(),
        value=df['year'].min(),
        marks={str(year): str(year) for year in df['year'].unique()},
        step=None),

    html.Br(),

    ### MAP ###

    html.Div(children='Emissions by year and gas',
             style = {'textAlign': 'center'}),

    html.Label('Choose a Projection'),
    dcc.RadioItems(
        id='projection',
        options=[dict(label='Equirectangular', value=0), dict(label='Orthographic', value=1)],
        value=0,
        labelStyle={'display': 'inline-block'}
    ),

    html.Label('Select a Gas'),
    dcc.RadioItems(
        id='checkbox_gases',
        options=gases,
        value='GHG Emissions',
        labelStyle={'display': 'inline-block'}
    ),


    dcc.Graph(id = 'map'),

    ### BAR PLOT ###

    html.Br(),

    html.Hr(),

    html.Div(children='Emissions by country and year',
             style = {'textAlign': 'center'}),

    html.Label('Choose a country and a year'),
    dcc.Dropdown(
        id='country_drop',
        options=country_options,
        value='Portugal',
        multi=False
    ),

    #html.Label('Choose a scale'),
    #dcc.RadioItems(
        #id='lin_log',
        #options=[dict(label='Linear', value=0), dict(label='Log', value=1)],
        #value=0,
    #labelStyle={'display': 'inline-block'}
    #),

    dcc.Graph(id = 'bar_plot'),


    dcc.Graph(id = 'bar_plot_cont')])

@app.callback([Output("bar_plot", "figure"), Output('map', 'figure'), Output("bar_plot_cont", "figure")],
              [Input("country_drop", "value"),
               Input("year_slider", "value"),
               #Input("lin_log", "value"),
               Input("projection", "value"),
               Input('checkbox_gases', 'value')])

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


    return fig, map, bar_cont


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

