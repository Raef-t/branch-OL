import 'package:flutter/material.dart';
import '/core/helpers/build_bottom_navigation_bar_item_in_class_view_helper.dart';
import '/core/helpers/show_little_black_view_with_widget_helper.dart';
import '/core/lists/elements_for_item_in_bottom_navigation_bar_in_class_view_list.dart';
import '/features/class/presentation/view/widgets/custom_message_widget_in_bottom_navigation_bar_in_class_view.dart';
import '/features/class/presentation/view/widgets/custom_take_presence_widget_in_bottom_navigation_bar_in_class_view.dart';

class CustomBottomNavigationBarInClassView extends StatefulWidget {
  const CustomBottomNavigationBarInClassView({super.key});

  @override
  State<CustomBottomNavigationBarInClassView> createState() =>
      _CustomBottomNavigationBarInClassViewState();
}

class _CustomBottomNavigationBarInClassViewState
    extends State<CustomBottomNavigationBarInClassView> {
  int currentIndex = 0;
  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: const BoxDecoration(
        color: Colors.white,
        border: Border(top: BorderSide(color: Colors.white, width: 1)),
        boxShadow: const [
          BoxShadow(
            color: Colors.black26,
            blurRadius: 10,
            offset: Offset(0, 4),
          ),
        ],
      ),

      // height: MediaQuery.sizeOf(context).height * 0.14,
      child: BottomNavigationBar(
        useLegacyColorScheme: false,
        backgroundColor: Colors.white,
        elevation: 0,
        currentIndex: currentIndex,
        showSelectedLabels: false,
        showUnselectedLabels: false,
        items: List.generate(
          elementsForItemInBottomNavigationBarInClassViewList.length,
          (index) {
            final item =
                elementsForItemInBottomNavigationBarInClassViewList[index];
            return buildBottomNavigationBarItemInClassViewHelper(
              image: item['image'],
              text: item['text'],
            );
          },
        ),
        onTap: (index) {
          setState(() => currentIndex = index);
          if (index == 0) {
            showLittleBlackViewWithWidgetHelper(
              context: context,
              widget:
                  const CustomMessageWidgetInBottomNavigationBarInClassView(),
            );
          } else {
            showLittleBlackViewWithWidgetHelper(
              context: context,
              widget:
                  const CustomTakePresenceWidgetInBottomNavigationBarInClassView(),
            );
          }
        },
      ),
    );
  }
}
