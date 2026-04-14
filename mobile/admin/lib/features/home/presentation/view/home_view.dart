import 'dart:io';
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import '/core/components/child_and_bottom_navigation_bar_in_ios_component.dart';
import '/core/components/cupertino_page_scaffold_with_child_component.dart';
import '/core/components/scaffold_with_body_and_bottom_navigation_bar_component.dart';
import '/core/lists/custom_view_bodies_list.dart';

class HomeView extends StatefulWidget {
  const HomeView({super.key});

  @override
  State<HomeView> createState() => _HomeViewState();
}

class _HomeViewState extends State<HomeView> {
  int currentIndex = 0;
  void onTap(int index) {
    setState(() => currentIndex = index);
  }

  @override
  Widget build(BuildContext context) {
    if (Platform.isIOS) {
      return CupertinoPageScaffoldWithChildComponent(
        child: ChildAndBottomNavigationBarInIosComponent(
          widget: customViewBodiesList[currentIndex],
          currentIndex: currentIndex,
          onTap: onTap,
        ),
      );
    } else {
      return ScaffoldWithBodyAndBottomNavigationBarComponent(
        body: customViewBodiesList[currentIndex],
        currentIndex: currentIndex,
        onTap: onTap,
      );
    }
  }
}
