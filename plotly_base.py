import pandas as pd

import plotly.graph_objs as go
import plotly.offline as pyo

import numpy as np

df = pd.read_csv('emission_full.csv')

#print(df.columns)
#print(df[df.isna().any(axis = 1)]) # São os do WORLD
df['continent'] = df['continent'].fillna('World')



#print(df.continent.unique())

df_grouped = df.groupby(['continent', 'year'])['GHG_emissions', 'CH4_emissions','N2O_emissions', 'F_Gas_emissions', 'CO2_emissions'].sum().reset_index()
#print(df_grouped)

df_grouped = df_grouped[df_grouped['year'] == 2014]



df_ = df[(df['year'] == 2014) & (df['country_name'] == 'China')].reset_index()

#print(df_)s

print(type(float(df_['GHG_emissions'][0])))


continents =['Africa', 'Asia','Europe', 'North America', 'Oceania', 'Seven seas (open ocean)', 'South America', 'World']

fig = go.Figure(data=[
    go.Bar(name= 'GHG Emissions', x=continents, y= df_grouped['GHG_emissions']),
    go.Bar(name= df_['country_name'][0] + ' GHG emissions', x=continents, y=float(df_['GHG_emissions'])*np.ones(8))])
# Change the bar mode
fig.update_layout(barmode= 'group')
pyo.plot(fig)


"""
fig = go.Figure()

fig.add_trace(go.Bar(
    x = [df_.iloc[0]['year']],
    y = [df_.iloc[0]['CH4_emissions']],
    name = 'CH4 Emissions'))

fig.add_trace(go.Bar(
    x = [df_.iloc[0]['year']],
    y = [df_.iloc[0]['N2O_emissions']],
    name = 'N20 Emissions'))

fig.add_trace(go.Bar(
    x = [df_.iloc[0]['year']],
    y = [df_.iloc[0]['F_Gas_emissions']],
    name = 'F Gas Emissions'))

fig.add_trace(go.Bar(
    x = [df_.iloc[0]['year']],
    y = [df_.iloc[0]['CO2_emissions']],
    name = 'CO2 Emissions'))

pyo.plot(fig)

"""


