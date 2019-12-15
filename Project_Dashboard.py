import plotly.graph_objs as go
import pandas as pd
import plotly.offline as pyo
from plotly.subplots import make_subplots
import numpy as np
import plotly.express as px
import dash_html_components as html
import dash_core_components as dcc
import dash


df = pd.read_csv('D:/Daten/Maren/NOVA/Data Visualization/Practical/Lab4/emission_full.csv')

fig2=go.Figure(data=go.Choropleth(locations=['Portugal'], z=[100], showscale=False, locationmode='country names'))
fig_bar = go.Figure(data=go.Bar)
"""
fig=make_subplots(rows=2, cols=2,
                  row_heights=[0.7,0.3],
                  shared_xaxes=True, shared_yaxes=True,
                  subplot_titles=['World Map', 'Country details', 'EU - Comparison'],
                  specs=[[{'type':'scatter', 'rowspan':2}, {'type':'scatter'}],
                         [None , {'type':'scatter'}]])

fig.add_trace(fig2.show(),
    row=1, col=1
)

fig.add_trace(
    go.Scatter(x=[20, 30, 40], y=[50, 60, 70]),
    row=1, col=2
)

fig.add_trace(
    go.Scatter(x=[20, 30, 40], y=[50, 60, 70]),
    row=2, col=2
)
"""

"""
# World map with density
df_world = pd.read_csv('https://raw.githubusercontent.com/plotly/datasets/master/earthquakes-23k.csv')
fig_world = px.density_mapbox(df_world, lat='Latitude', lon='Longitude', z='Magnitude',
                              radius=10, center=dict(lat=0,lon=180),
                              zoom=0, mapbox_style='stamen-terrain')
"""
# create app
app = dash.Dash()
app.layout = html.Div([
    html.Div([
        html.H1('DV Project - Climate Change')
    ], id='title_division'),

    html.Div([
        dcc.Dropdown(
            options=[
                # create dicts for each country in dataset
                {'label': i, 'value': i}for i in df['country_name'].unique()
            ],
            multi=True,
            value='Portugal'
        ),
    ], id='Multi-Dropdown'),

    html.Div([
        dcc.Graph(id='country_display', figure=fig_bar)
    ], id='graph-division'),

    html.Div([
        dcc.Slider(
            min=df['year'].min(),
            max=df['year'].max(),
            value= 2000,
        )
    ], id='year_slider'),

    html.Div([
        dcc.Graph(id='bar_plot', figure=fig)
    ])
])
"""

#fig.update_layout(height=100, width=800, title_text="World Emissions")

"""
if __name__ == '__main__':
    app.run_server(debug=True)
